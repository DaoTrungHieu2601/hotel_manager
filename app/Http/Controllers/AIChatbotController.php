<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ChatConversation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Service;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AIChatbotController extends Controller
{
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message'          => ['required', 'string', 'max:1000'],
            'history'          => ['nullable', 'array', 'max:20'],
            'history.*.role'   => ['required', 'in:user,model'],
            'history.*.text'   => ['required', 'string', 'max:2000'],
        ]);

        $setting = SiteSetting::instance();

        if (! $setting->chatbot_enabled) {
            return response()->json(['error' => 'Chatbot chưa được kích hoạt.'], 403);
        }

        $apiKey = $setting->chatbot_gemini_api_key;
        if (! $apiKey) {
            return response()->json(['error' => 'Chưa cấu hình API key cho chatbot.'], 503);
        }

        $contents = [];
        foreach (($request->input('history') ?? []) as $turn) {
            $contents[] = [
                'role'  => $turn['role'],
                'parts' => [['text' => $turn['text']]],
            ];
        }
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $request->input('message')]],
        ];

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $this->buildSystemPrompt($setting)]],
            ],
            'contents'           => $contents,
            'generationConfig'   => [
                'temperature'     => 0.7,
                'maxOutputTokens' => 600,
            ],
        ];

        try {
            $response = Http::timeout(20)
                ->withOptions(['verify' => false])
                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key='.$apiKey,
                    $payload
                );
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json(['error' => 'Không thể kết nối đến Gemini AI. Kiểm tra kết nối mạng của máy chủ.'], 502);
        }

        if ($response->failed()) {
            if ($response->status() === 429) {
                return response()->json(['error' => 'AI đang bận, vui lòng thử lại sau vài giây.'], 429);
            }
            if ($response->status() === 400) {
                return response()->json(['error' => 'Câu hỏi không hợp lệ. Vui lòng thử lại.'], 400);
            }
            $errMsg = $response->json('error.message') ?? 'Lỗi từ Gemini AI (HTTP '.$response->status().').';
            return response()->json(['error' => $errMsg], 502);
        }

        $text = $response->json('candidates.0.content.parts.0.text')
            ?? 'Xin lỗi, tôi không hiểu câu hỏi này.';

        // ── Lưu hội thoại vào DB nếu khách đã đăng nhập ───────────────
        $user = $request->user();
        if ($user && $user->isCustomer()) {
            $conversation = ChatConversation::firstOrCreate(
                ['user_id' => $user->id],
                ['last_message_at' => now()]
            );

            // Tin nhắn của khách
            $conversation->messages()->create([
                'sender_id' => $user->id,
                'is_admin'  => false,
                'body'      => $request->input('message'),
            ]);

            // Phản hồi AI (sender_id = null để phân biệt với admin thật)
            $aiMsg = $conversation->messages()->create([
                'sender_id' => null,
                'is_admin'  => true,
                'body'      => $text,
            ]);

            $conversation->update(['last_message_at' => now()]);

            return response()->json([
                'reply'  => $text,
                'msg_id' => $aiMsg->id,
            ]);
        }

        return response()->json(['reply' => $text]);
    }

    // ─────────────────────────────────────────────
    //  BUILD SYSTEM PROMPT — lấy toàn bộ dữ liệu thực
    // ─────────────────────────────────────────────
    private function buildSystemPrompt(SiteSetting $setting): string
    {
        $today     = Carbon::today();
        $hotelName = $setting->displayName();
        $phone     = $setting->site_phone  ?: 'xem website';
        $email     = $setting->site_email  ?: 'xem website';
        $address   = $setting->site_address ?: '';
        $website   = $setting->site_website ?: '';
        $tagline   = $setting->site_tagline ?: '';
        $lines     = [];

        // ── Phần tùy chỉnh của admin (nếu có) ─────────────────────────
        $customPrompt = trim((string) ($setting->chatbot_system_prompt ?? ''));

        // ── VAI TRÒ ───────────────────────────────────────────────────
        $lines[] = "Bạn là trợ lý tư vấn của \"{$hotelName}\".";
        if ($customPrompt !== '') {
            $lines[] = '';
            $lines[] = $customPrompt;
        }
        if ($setting->chatbot_training_file_path && Storage::disk('public')->exists($setting->chatbot_training_file_path)) {
            $trainingText = trim((string) Storage::disk('public')->get($setting->chatbot_training_file_path));
            if ($trainingText !== '') {
                $lines[] = '';
                $lines[] = 'DU LIEU HUAN LUYEN BO SUNG TU FILE .TXT (admin tai len):';
                // Guard prompt size to keep request stable.
                $lines[] = mb_substr($trainingText, 0, 120000);
            }
        }

        // ── THÔNG TIN CỬA HÀNG / KHÁCH SẠN ──────────────────────────
        $lines[] = '';
        $lines[] = 'THÔNG TIN KHÁCH SẠN (bắt buộc dùng đúng, không được bịa):';
        $lines[] = "- Tên: {$hotelName}";
        if ($tagline)  $lines[] = "- Slogan: {$tagline}";
        if ($address)  $lines[] = "- Địa chỉ: {$address}";
        $lines[] = "- Hotline: {$phone}";
        if ($email !== 'xem website')  $lines[] = "- Email: {$email}";
        if ($website)  $lines[] = "- Website: {$website}";
        $lines[] = '- Giờ làm việc: 24/7 (lễ tân trực tiếp), đặt phòng online bất kỳ lúc nào';

        // ── DANH SÁCH PHÒNG (lấy từ DB) ──────────────────────────────
        $roomTypes = RoomType::query()->with('rooms')->orderBy('default_price')->get();
        if ($roomTypes->isNotEmpty()) {
            $lines[] = '';
            $lines[] = 'LOẠI PHÒNG & GIÁ:';
            foreach ($roomTypes as $type) {
                $price  = number_format((float) $type->default_price, 0, ',', '.');
                $rooms  = $type->rooms;
                $avail  = $rooms->where('status', Room::STATUS_AVAILABLE)->count();
                $total  = $rooms->count();

                $line = "- {$type->name}: {$price} ₫/đêm, tối đa {$type->max_occupancy} khách";
                if ($type->beds) $line .= ", {$type->beds} giường";
                $line .= " ({$avail}/{$total} phòng đang trống)";
                $lines[] = $line;

                if ($type->description) {
                    $lines[] = "  + Mô tả: {$type->description}";
                }
                $fac = RoomType::linesFromText($type->facilities);
                if (! empty($fac)) {
                    $lines[] = '  + Tiện nghi: '.implode(', ', array_slice($fac, 0, 8));
                }
                $ame = RoomType::linesFromText($type->amenities);
                if (! empty($ame)) {
                    $lines[] = '  + Trang thiết bị: '.implode(', ', array_slice($ame, 0, 8));
                }

                // Phòng trống cụ thể
                $codes = $rooms->where('status', Room::STATUS_AVAILABLE)->pluck('code')->take(5)->implode(', ');
                if ($codes) $lines[] = "  + Phòng trống hiện tại: {$codes}";

                // Đơn đặt tương lai
                $future = Booking::query()
                    ->where('room_type_id', $type->id)
                    ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_PENDING])
                    ->where('check_in', '>', $today)->count();
                if ($future > 0) $lines[] = "  + Đã có {$future} đơn đặt trước sắp tới";
            }
        }

        // ── DỊCH VỤ (lấy từ DB) ──────────────────────────────────────
        $services = Service::query()->where('is_active', true)->orderBy('name')->get();
        if ($services->isNotEmpty()) {
            $lines[] = '';
            $lines[] = 'DỊCH VỤ KÈM THEO:';
            foreach ($services as $svc) {
                $lines[] = '- '.$svc->name.': '.number_format((float) $svc->price, 0, ',', '.').' ₫';
            }
        }

        // ── TÌNH TRẠNG HÔM NAY ───────────────────────────────────────
        $totalAvail = Room::query()->where('status', Room::STATUS_AVAILABLE)->count();
        $pending    = Booking::query()->where('status', Booking::STATUS_PENDING)->count();
        $lines[] = '';
        $lines[] = 'TÌNH TRẠNG HÔM NAY ('.now()->format('d/m/Y').'):';
        $lines[] = "- Tổng phòng trống: {$totalAvail}";
        if ($pending > 0) $lines[] = "- Đơn đang chờ xác nhận: {$pending}";

        // ── CHÍNH SÁCH ────────────────────────────────────────────────
        $lines[] = '';
        $lines[] = 'CHÍNH SÁCH & ĐẶT PHÒNG:';
        $lines[] = '- Check-in: 14:00 | Check-out: 12:00';
        $lines[] = '- Đặt phòng: vào mục "Tìm phòng" trên website → chọn ngày → xác nhận → gửi đơn';
        $lines[] = '- Thanh toán cọc: qua VNPAY hoặc không cọc (tuỳ chọn)';
        $lines[] = '- Hủy đơn: liên hệ lễ tân hoặc vào "Đơn của tôi" trên website';
        $lines[] = '- Hóa đơn PDF: phát sau khi trả phòng';

        // ── QUY TẮC TRẢ LỜI ─────────────────────────────────────────
        $lines[] = '';
        $lines[] = 'QUY TẮC:';
        $lines[] = '- Chỉ trả lời về khách sạn, phòng, dịch vụ, đặt phòng — KHÔNG bịa thông tin';
        $lines[] = "- Nếu không chắc, nói \"liên hệ hotline {$phone} để được hỗ trợ\"";
        $lines[] = '- Trả lời ngắn gọn, thân thiện, bằng tiếng Việt';
        $lines[] = '- KHÔNG bịa giá, địa chỉ, số điện thoại hay thông tin không có trong dữ liệu trên';

        return implode("\n", $lines);
    }
}

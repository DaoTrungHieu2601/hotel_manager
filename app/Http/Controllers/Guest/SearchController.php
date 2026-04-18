<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function suggest(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 1) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // ── Loại phòng ──────────────────────────────────────────────────
        $roomTypes = RoomType::query()
            ->where(fn($w) => $w
                ->where('name', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")
                ->orWhere('facilities', 'like', "%{$q}%")
            )
            ->limit(4)
            ->get();

        foreach ($roomTypes as $rt) {
            $results[] = [
                'type'     => 'room',
                'icon'     => '🛏️',
                'label'    => $rt->name,
                'sub'      => number_format($rt->default_price, 0, ',', '.') . 'đ / đêm · Tối đa ' . $rt->max_occupancy . ' khách',
                'url'      => route('guest.search-rooms'),
                'badge'    => 'Loại phòng',
                'badge_color' => '#7c3aed',
            ];
        }

        // ── Dịch vụ ─────────────────────────────────────────────────────
        $services = Service::query()
            ->where('is_active', true)
            ->where('name', 'like', "%{$q}%")
            ->limit(4)
            ->get();

        foreach ($services as $svc) {
            $results[] = [
                'type'     => 'service',
                'icon'     => '🍽️',
                'label'    => $svc->name,
                'sub'      => number_format($svc->price, 0, ',', '.') . 'đ / lần',
                'url'      => route('guest.search-rooms'),
                'badge'    => 'Dịch vụ',
                'badge_color' => '#0891b2',
            ];
        }

        // ── Trang tĩnh ──────────────────────────────────────────────────
        $pages = [
            ['label' => 'Trang chủ',        'sub' => 'Xem thông tin khách sạn',      'url' => route('home'),              'icon' => '🏠'],
            ['label' => 'Tìm phòng',         'sub' => 'Tìm & đặt phòng theo ngày',   'url' => route('guest.search-rooms'), 'icon' => '🔍'],
            ['label' => 'Đăng nhập',         'sub' => 'Đăng nhập tài khoản',          'url' => route('login'),              'icon' => '🔐'],
            ['label' => 'Đăng ký',           'sub' => 'Tạo tài khoản mới',            'url' => route('register'),           'icon' => '📝'],
        ];

        foreach ($pages as $page) {
            if (Str::contains(mb_strtolower($page['label']), mb_strtolower($q))
                || Str::contains(mb_strtolower($page['sub']), mb_strtolower($q))) {
                $page['type']       = 'page';
                $page['badge']      = 'Trang';
                $page['badge_color']= '#475569';
                $results[] = $page;
            }
        }

        return response()->json(['results' => $results]);
    }
}

<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredTypes = RoomType::query()->orderBy('id')->take(3)->get();

        $setting = SiteSetting::instance();
        $homeHeroBgUrl = asset('images/home-hero-default.jpg');
        if ($setting->bg_home_path && Storage::disk('public')->exists($setting->bg_home_path)) {
            $homeHeroBgUrl = asset('storage/'.$setting->bg_home_path);
        }

        $facilities = $this->decodeJsonArray($setting->home_facilities_items, [
            [
                'title' => 'Hồ bơi vô cực (06:00 - 21:00)',
                'desc' => 'Đắm mình trong làn nước xanh mát và ngắm nhìn toàn cảnh thành phố từ trên cao. Khu vực an toàn dành riêng cho trẻ em luôn sẵn sàng.',
                'image' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1400&q=80',
            ],
            [
                'title' => 'Sky Bar (17:00 - 00:30)',
                'desc' => 'Không gian cực "chill" về đêm với những ly cocktail tuyệt hảo và những đêm nhạc acoustic đầy cảm xúc.',
                'image' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=1400&q=80',
            ],
            [
                'title' => 'Nhà hàng All-day Dining (06:00 - 22:30)',
                'desc' => 'Đánh thức vị giác với tiệc buffet sáng đa dạng và thực đơn A La Carte kết hợp tinh hoa ẩm thực Á - Âu.',
                'image' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=1400&q=80',
            ],
            [
                'title' => 'Business Center & Hội Nghị',
                'desc' => 'Không gian chuyên nghiệp, được trang bị đầy đủ thiết bị âm thanh, ánh sáng hiện đại. Sức chứa linh hoạt lên đến 250 khách, lý tưởng cho các sự kiện doanh nghiệp.',
                'image' => 'https://images.unsplash.com/photo-1517502884422-41eaead166d4?auto=format&fit=crop&w=1400&q=80',
            ],
        ]);

        $offers = $this->decodeJsonArray($setting->home_offers_items, [
            [
                'title' => 'Gói Doanh Nhân (Business Package)',
                'subtitle' => 'Tối ưu thời gian, nâng tầm đẳng cấp cho chuyến công tác.',
                'benefits' => 'Nâng hạng phòng Executive miễn phí; Miễn phí 2 giờ sử dụng phòng họp nhỏ; Check-in ưu tiên.',
            ],
            [
                'title' => 'Gói Gia Đình (Family Gateway)',
                'subtitle' => 'Không gian kết nối hoàn hảo cho cả gia đình.',
                'benefits' => 'Giảm ngay 15% khi đặt phòng Family Connect; Miễn phí ăn sáng cho 2 trẻ em dưới 11 tuổi.',
            ],
            [
                'title' => 'Gói Lưu Trú Dài Ngày (Long-stay Promo)',
                'subtitle' => 'Trải nghiệm trọn vẹn hơn với mức giá tiết kiệm hơn.',
                'benefits' => 'Giảm 20% tổng hóa đơn cho kỳ nghỉ từ 5 đêm trở lên; Tặng voucher dịch vụ Spa & Giặt ủi.',
            ],
        ]);

        $testimonials = $this->decodeJsonArray($setting->home_testimonials_items, [
            [
                'quote' => 'Phòng ốc cực kỳ sạch sẽ, thiết kế hiện đại và tinh tế. Tôi rất ấn tượng với sự hỗ trợ nhiệt tình của các bạn lễ tân. Chắc chắn sẽ quay lại trong chuyến công tác tới!',
                'author' => 'Nguyễn Hoàng Minh',
                'role' => 'Khách công tác',
            ],
            [
                'quote' => 'Gia đình tôi đã có một kỳ nghỉ cuối tuần tuyệt vời ở phòng Family Connect. Buffet sáng đa dạng, hồ bơi sạch và an toàn cho bé. Vị trí trung tâm đi lại rất tiện.',
                'author' => 'Trần Thu Hà',
                'role' => 'Du lịch gia đình',
            ],
            [
                'quote' => 'Không gian yên tĩnh giữa lòng thành phố nhộn nhịp. Tôi rất thích khu vực Sky Bar buổi tối. Mọi dịch vụ đều đúng chuẩn 4 sao, rất xứng đáng với giá tiền.',
                'author' => 'David Smith',
                'role' => 'Du khách quốc tế',
            ],
        ]);

        $faqItems = $this->decodeJsonArray($setting->home_faq_items, [
            [
                'q' => 'Giờ nhận/trả phòng của khách sạn là mấy giờ?',
                'a' => 'Giờ nhận phòng (Check-in) tiêu chuẩn là 14:00 và giờ trả phòng (Check-out) là 12:00. Bạn có thể yêu cầu nhận phòng sớm hoặc trả phòng muộn tùy thuộc vào tình trạng phòng trống.',
            ],
            [
                'q' => 'Khách sạn có cho mang thú cưng không?',
                'a' => 'Một số hạng phòng nhất định cho phép mang theo thú cưng nhỏ (có áp dụng phụ phí). Vui lòng thông báo trước khi đặt phòng để chúng tôi sắp xếp phù hợp nhất.',
            ],
            [
                'q' => 'Chính sách hủy phòng được quy định như thế nào?',
                'a' => 'Đối với các gói giá linh hoạt, bạn được hủy miễn phí trước 48 giờ. Các gói ưu đãi đặc biệt (không hoàn hủy) sẽ không được hoàn tiền. Chi tiết sẽ được hiển thị khi bạn chọn phòng.',
            ],
            [
                'q' => 'Khách sạn có dịch vụ xe đưa đón sân bay không?',
                'a' => 'Có. EAUT HOTEL cung cấp dịch vụ đưa đón sân bay tận nơi theo yêu cầu. Vui lòng cung cấp mã chuyến bay cho chúng tôi trước 24 giờ.',
            ],
            [
                'q' => 'Tôi có thể xuất hóa đơn VAT cho công ty được không?',
                'a' => 'Khách sạn hỗ trợ xuất hóa đơn điện tử. Vui lòng cung cấp đầy đủ thông tin (Tên đơn vị, Mã số thuế, Địa chỉ, Email) tại thời điểm nhận phòng hoặc trả phòng.',
            ],
        ]);

        $locationDistances = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($setting->home_location_distances ?? '')))));
        if ($locationDistances === []) {
            $locationDistances = [
                'Khu phố đi bộ & Ẩm thực đêm: Cách 2 km (Khoảng 5 phút di chuyển)',
                'Trung tâm thương mại lớn: Cách 3 km (Khoảng 10 phút di chuyển)',
                'Bảo tàng & Nhà hát thành phố: Cách 4 km (Khoảng 12 phút di chuyển)',
                'Sân bay Quốc tế: Cách 35 phút lái xe trong điều kiện giao thông tiêu chuẩn.',
            ];
        }

        return view('guest.home', [
            'featuredTypes' => $featuredTypes,
            'homeHeroBgUrl' => $homeHeroBgUrl,
            'facilitiesTitle' => $setting->home_facilities_title ?: 'Trải Nghiệm Tiện Ích Đẳng Cấp',
            'facilitiesItems' => $facilities,
            'locationTitle' => $setting->home_location_title ?: 'Tâm Điểm Kết Nối, Dễ Dàng Khám Phá',
            'locationDescription' => $setting->home_location_description ?: 'Nằm ngay trái tim thành phố, EAUT HOTEL mang đến sự thuận tiện tối đa cho hành trình công tác hay nghỉ dưỡng của bạn.',
            'locationMapUrl' => $setting->home_location_map_url ?: 'https://maps.google.com/maps?q=10.7769,106.7009&z=14&output=embed',
            'locationDistances' => $locationDistances,
            'locationCtaLabel' => $setting->home_location_cta_label ?: 'Đặt xe đưa đón sân bay 24/7',
            'locationCtaLink' => $setting->home_location_cta_link ?: 'tel:1900888127',
            'offersTitle' => $setting->home_offers_title ?: 'Ưu Đãi Độc Quyền Dành Cho Bạn',
            'offersItems' => $offers,
            'testimonialsTitle' => $setting->home_testimonials_title ?: 'Khách Hàng Nói Gì Về EAUT HOTEL',
            'testimonialsItems' => $testimonials,
            'faqTitle' => $setting->home_faq_title ?: 'Câu Hỏi Thường Gặp',
            'faqItems' => $faqItems,
        ]);
    }

    private function decodeJsonArray(?string $value, array $fallback): array
    {
        $decoded = json_decode((string) $value, true);
        if (! is_array($decoded)) {
            return $fallback;
        }

        $normalized = array_values(array_filter($decoded, fn ($item) => is_array($item)));

        return $normalized !== [] ? $normalized : $fallback;
    }
}

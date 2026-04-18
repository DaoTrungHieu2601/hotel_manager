<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Bước 3 spec: 3 loại phòng — slug cố định để chạy db:seed nhiều lần không trùng
        $types = [
            [
                'slug' => 'standard',
                'name' => 'Standard',
                'price' => 890_000,
                'beds' => 1,
                'max' => 2,
                'description' => 'Phòng Standard gọn gàng, phù hợp công tác hoặc nghỉ ngắn ngày. Cửa sổ hướng phố hoặc sân trong yên tĩnh.',
                'facilities' => "Giường đôi hoặc 2 giường đơn\nĐiều hòa 2 chiều\nTV màn hình phẳng 32 inch\nTủ quần áo, két an toàn mini\nPhòng tắm vòi sen\nDầu gội, sữa tắm, đồ dùng cá nhân",
                'amenities' => "Wi‑Fi tốc độ cao miễn phí\nNước uống miễn phí (2 chai/ngày)\nỔ cắm đa năng gần bàn làm việc\nDịch vụ dọn phòng hàng ngày",
            ],
            [
                'slug' => 'deluxe',
                'name' => 'Deluxe',
                'price' => 1_590_000,
                'beds' => 2,
                'max' => 3,
                'description' => 'Phòng Deluxe rộng rãi, nội thất hiện đại, view đẹp. Lý tưởng cho gia đình nhỏ hoặc cặp đôi muốn không gian thoải mái hơn.',
                'facilities' => "2 giường đơn hoặc 1 giường king\nSofa nhỏ hoặc ghế đọc sách\nĐiều hòa, smart TV 43 inch\nMinibar (tính phí theo sử dụng)\nPhòng tắm có vòi sen và bồn\nÁo choàng tắm, dép đi trong phòng",
                'amenities' => "Wi‑Fi & truyền hình đa kênh\nNước nóng lạnh 24/7\nBàn là, ủi (theo yêu cầu)\nCheck-in nhanh cho khách đặt trước",
            ],
            [
                'slug' => 'suite',
                'name' => 'Suite',
                'price' => 2_590_000,
                'beds' => 2,
                'max' => 4,
                'description' => 'Suite sang trọng với phòng khách riêng, phù hợp gia đình hoặc khách cần không gian làm việc và tiếp khách tại phòng.',
                'facilities' => "Phòng ngủ + phòng khách tách biệt\nGiường king, sofa bed tùy cấu hình\nBếp nhỏ / tủ lạnh mini (một số phòng)\n2 TV, hệ thống âm thanh Bluetooth\n2 phòng tắm hoặc phòng tắm + WC riêng\nBan công hoặc cửa sổ panorama (tùy phòng)",
                'amenities' => "Wi‑Fi tối đa băng thông\nĐưa hành lý, gọi xe (theo chính sách)\nƯu tiên chọn tầng & view\nDịch vụ concierge qua lễ tân 24/7",
            ],
        ];

        $roomTypes = collect($types)->map(function (array $row) {
            return RoomType::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'facilities' => $row['facilities'],
                    'amenities' => $row['amenities'],
                    'default_price' => $row['price'],
                    'beds' => $row['beds'],
                    'max_occupancy' => $row['max'],
                    'image_path' => null,
                ]
            );
        });

        $standard = $roomTypes->firstWhere('slug', 'standard');
        $deluxe = $roomTypes->firstWhere('slug', 'deluxe');
        $suite = $roomTypes->firstWhere('slug', 'suite');

        $assignments = [
            ...array_fill(0, 4, $standard->id),
            ...array_fill(0, 3, $deluxe->id),
            ...array_fill(0, 3, $suite->id),
        ];

        foreach (range(101, 110) as $i => $number) {
            $typeId = $assignments[$i];
            Room::query()->updateOrCreate(
                ['code' => (string) $number],
                [
                    'room_type_id' => $typeId,
                    'floor' => (string) (intdiv((int) $number, 100)),
                    'status' => Room::STATUS_AVAILABLE,
                ]
            );
        }

        $services = [
            ['name' => 'Nước suối', 'price' => 25_000],
            ['name' => 'Giặt ủi', 'price' => 80_000],
            ['name' => 'Minibar', 'price' => 50_000],
            ['name' => 'Đưa đón sân bay', 'price' => 350_000],
        ];
        foreach ($services as $s) {
            Service::query()->updateOrCreate(
                ['name' => $s['name']],
                ['price' => $s['price'], 'is_active' => true]
            );
        }

        User::query()->updateOrCreate(
            ['email' => 'admin@hotel.test'],
            [
                'name' => 'Quản trị viên',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'phone' => '0900000001',
                'address' => __('Hà Nội'),
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'customer1@hotel.test'],
            [
                'name' => 'Khách A',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
                'phone' => '0900000011',
                'address' => null,
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'customer2@hotel.test'],
            [
                'name' => 'Khách B',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
                'phone' => '0900000012',
                'address' => null,
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'reception@hotel.test'],
            [
                'name' => 'Lễ tân 01',
                'password' => Hash::make('password'),
                'role' => User::ROLE_RECEPTIONIST,
                'phone' => '0900000002',
                'address' => null,
                'email_verified_at' => now(),
            ]
        );

        $customer = User::query()->where('email', 'customer1@hotel.test')->firstOrFail();

        Booking::query()->firstOrCreate(
            [
                'user_id' => $customer->id,
                'room_type_id' => $standard->id,
                'status' => Booking::STATUS_PENDING,
            ],
            [
                'check_in' => now()->addDay(),
                'check_out' => now()->addDays(3),
                'guests' => 2,
                'deposit_amount' => 500_000,
            ]
        );
    }
}

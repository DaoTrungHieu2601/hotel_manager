<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    public const STATUS_AVAILABLE = 'available';

    public const STATUS_OCCUPIED = 'occupied';

    public const STATUS_BOOKED = 'booked';

    public const STATUS_CLEANING = 'cleaning';

    public const STATUS_MAINTENANCE = 'maintenance';

    protected $fillable = [
        'room_type_id', 'code', 'floor', 'status', 'notes',
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_AVAILABLE => __('Trống'),
            self::STATUS_OCCUPIED => __('Đang có khách'),
            self::STATUS_BOOKED => __('Đã đặt trước'),
            self::STATUS_CLEANING => __('Đang dọn dẹp'),
            self::STATUS_MAINTENANCE => __('Bảo trì'),
        ];
    }
}

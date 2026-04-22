<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_CHECKED_IN = 'checked_in';

    public const STATUS_CHECKED_OUT = 'checked_out';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id', 'room_type_id', 'room_id', 'check_in', 'check_out', 'guests', 'status',
        'deposit_amount', 'deposit_paid_at', 'payment_method', 'rate_per_night', 'guest_notes',
        'guest_planned_check_in', 'guest_planned_check_out',
        'confirmed_at', 'checked_in_at', 'checked_out_at',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'guests' => 'integer',
            'deposit_amount' => 'decimal:2',
            'deposit_paid_at' => 'datetime',
            'rate_per_night' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function bookingServices(): HasMany
    {
        return $this->hasMany(BookingService::class);
    }

    /**
     * Quan hệ nhiều–nhiều với dịch vụ (bảng trung gian booking_services).
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'booking_services')
            ->withPivot(['id', 'quantity', 'unit_price'])
            ->withTimestamps();
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function nights(): int
    {
        return max(1, $this->check_in->diffInDays($this->check_out));
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /** Cọc > 0 nhưng chưa ghi nhận thanh toán (luồng demo VNPAY/MOMO). */
    public function depositOutstanding(): bool
    {
        return (float) $this->deposit_amount > 0 && $this->deposit_paid_at === null;
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => __('Đang hoàn tất đặt phòng'),
            self::STATUS_PENDING => __('Chờ xác nhận'),
            self::STATUS_CONFIRMED => __('Đã xác nhận'),
            self::STATUS_CHECKED_IN => __('Đang lưu trú'),
            self::STATUS_CHECKED_OUT => __('Đã trả phòng'),
            self::STATUS_CANCELLED => __('Đã hủy'),
        ];
    }

    public function overlapsDates(\Carbon\CarbonInterface $from, \Carbon\CarbonInterface $to): bool
    {
        return $this->check_in->lt($to) && $this->check_out->gt($from);
    }
}

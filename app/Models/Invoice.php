<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'booking_id', 'invoice_number', 'nights', 'room_subtotal', 'services_subtotal', 'early_late_subtotal', 'deposit', 'total', 'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'nights' => 'integer',
            'room_subtotal' => 'decimal:2',
            'services_subtotal' => 'decimal:2',
            'early_late_subtotal' => 'decimal:2',
            'deposit' => 'decimal:2',
            'total' => 'decimal:2',
            'issued_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}

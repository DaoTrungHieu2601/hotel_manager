<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $fillable = [
        'name',
        'basic_salary',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}

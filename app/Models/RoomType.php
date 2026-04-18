<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'facilities', 'amenities', 'default_price', 'beds', 'max_occupancy', 'image_path',
    ];

    protected function casts(): array
    {
        return [
            'default_price' => 'decimal:2',
            'beds' => 'integer',
            'max_occupancy' => 'integer',
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * @return list<string>
     */
    public static function linesFromText(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text))));
    }
}

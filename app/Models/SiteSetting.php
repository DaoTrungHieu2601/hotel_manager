<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    private static ?self $instanceCache = null;

    protected $fillable = [
        'site_display_name',
        'site_tagline',
        'site_address',
        'site_phone',
        'site_email',
        'site_website',
        'bg_home_path',
        'bg_search_path',
        'bg_login_path',
        'bg_register_path',
        'chatbot_enabled',
        'chatbot_name',
        'chatbot_avatar_path',
        'chatbot_gemini_api_key',
        'chatbot_system_prompt',
        'chatbot_training_file_path',
        'home_facilities_title',
        'home_facilities_items',
        'home_location_title',
        'home_location_description',
        'home_location_map_url',
        'home_location_distances',
        'home_location_cta_label',
        'home_location_cta_link',
        'home_offers_title',
        'home_offers_items',
        'home_testimonials_title',
        'home_testimonials_items',
        'home_faq_title',
        'home_faq_items',
        'social_facebook',
        'social_zalo',
        'social_phone',
        'social_instagram',
        'social_enabled',
        'policy_check_in_start',
        'policy_check_in_end',
        'policy_check_out_start',
        'policy_check_out_end',
        'no_show_cutoff_time',
        'check_time_grace_minutes',
        'extra_hour_price',
    ];

    protected function casts(): array
    {
        return [
            'chatbot_enabled' => 'boolean',
            'social_enabled'  => 'boolean',
            'check_time_grace_minutes' => 'integer',
            'extra_hour_price' => 'decimal:2',
        ];
    }

    public static function instance(): self
    {
        return self::$instanceCache ??= (
            static::query()->first() ?? static::query()->create([])
        );
    }

    public static function forgetInstance(): void
    {
        self::$instanceCache = null;
    }

    public function displayName(): string
    {
        return $this->site_display_name !== null && $this->site_display_name !== ''
            ? $this->site_display_name
            : (string) config('app.name');
    }
}

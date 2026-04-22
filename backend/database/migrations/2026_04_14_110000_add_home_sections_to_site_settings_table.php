<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('home_facilities_title')->nullable()->after('chatbot_training_file_path');
            $table->text('home_facilities_items')->nullable()->after('home_facilities_title');
            $table->string('home_location_title')->nullable()->after('home_facilities_items');
            $table->text('home_location_description')->nullable()->after('home_location_title');
            $table->text('home_location_map_url')->nullable()->after('home_location_description');
            $table->text('home_location_distances')->nullable()->after('home_location_map_url');
            $table->string('home_location_cta_label')->nullable()->after('home_location_distances');
            $table->text('home_location_cta_link')->nullable()->after('home_location_cta_label');
            $table->string('home_offers_title')->nullable()->after('home_location_cta_link');
            $table->text('home_offers_items')->nullable()->after('home_offers_title');
            $table->string('home_testimonials_title')->nullable()->after('home_offers_items');
            $table->text('home_testimonials_items')->nullable()->after('home_testimonials_title');
            $table->string('home_faq_title')->nullable()->after('home_testimonials_items');
            $table->text('home_faq_items')->nullable()->after('home_faq_title');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};

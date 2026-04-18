<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        $setting = SiteSetting::instance();

        return view('admin.site-settings.edit', compact('setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $setting = SiteSetting::instance();

        $bgRules = $this->backgroundUploadRules();

        $request->validate([
            'site_display_name' => ['nullable', 'string', 'max:120'],
            'site_tagline' => ['nullable', 'string', 'max:500'],
            'site_address' => ['nullable', 'string', 'max:255'],
            'site_phone' => ['nullable', 'string', 'max:60'],
            'site_email' => ['nullable', 'string', 'email', 'max:120'],
            'site_website' => ['nullable', 'string', 'max:120'],
            'bg_home' => $bgRules,
            'bg_search' => $bgRules,
            'bg_login' => $bgRules,
            'bg_register' => $bgRules,
            'chatbot_name' => ['nullable', 'string', 'max:80'],
            'chatbot_gemini_api_key' => ['nullable', 'string', 'max:200'],
            'chatbot_system_prompt' => ['nullable', 'string', 'max:4000'],
            'chatbot_avatar' => $bgRules,
            'chatbot_training_file' => $this->chatbotTrainingUploadRules(),
            'home_facilities_title' => ['nullable', 'string', 'max:200'],
            'home_facilities_items' => ['nullable', 'string'],
            'home_location_title' => ['nullable', 'string', 'max:200'],
            'home_location_description' => ['nullable', 'string'],
            'home_location_map_url' => ['nullable', 'string'],
            'home_location_distances' => ['nullable', 'string'],
            'home_location_cta_label' => ['nullable', 'string', 'max:120'],
            'home_location_cta_link' => ['nullable', 'string', 'max:500'],
            'home_offers_title' => ['nullable', 'string', 'max:200'],
            'home_offers_items' => ['nullable', 'string'],
            'home_testimonials_title' => ['nullable', 'string', 'max:200'],
            'home_testimonials_items' => ['nullable', 'string'],
            'home_faq_title' => ['nullable', 'string', 'max:200'],
            'home_faq_items' => ['nullable', 'string'],
            'policy_check_in_start' => ['required', 'date_format:H:i'],
            'policy_check_in_end' => ['required', 'date_format:H:i'],
            'policy_check_out_start' => ['required', 'date_format:H:i'],
            'policy_check_out_end' => ['required', 'date_format:H:i'],
            'no_show_cutoff_time' => ['required', 'date_format:H:i'],
            'check_time_grace_minutes' => ['required', 'integer', 'min:0', 'max:240'],
            'extra_hour_price' => ['required', 'numeric', 'min:0'],
        ]);

        $setting->site_display_name = $request->input('site_display_name');
        $setting->site_tagline = $request->input('site_tagline');
        $setting->site_address = $request->input('site_address');
        $setting->site_phone   = $request->input('site_phone');
        $setting->site_email   = $request->input('site_email');
        $setting->site_website = $request->input('site_website');

        foreach ([
            'bg_home' => 'bg_home_path',
            'bg_search' => 'bg_search_path',
            'bg_login' => 'bg_login_path',
            'bg_register' => 'bg_register_path',
        ] as $uploadKey => $column) {
            if ($request->boolean('remove_'.$uploadKey)) {
                if ($setting->{$column}) {
                    Storage::disk('public')->delete($setting->{$column});
                }
                $setting->{$column} = null;
            }
            if ($request->hasFile($uploadKey)) {
                if ($setting->{$column}) {
                    Storage::disk('public')->delete($setting->{$column});
                }
                $setting->{$column} = $request->file($uploadKey)->store('site-backgrounds', 'public');
            }
        }

        $setting->chatbot_enabled = $request->boolean('chatbot_enabled');
        $setting->chatbot_name = $request->input('chatbot_name');
        $setting->chatbot_system_prompt = $request->input('chatbot_system_prompt');

        if ($request->input('chatbot_gemini_api_key') !== null) {
            $key = trim((string) $request->input('chatbot_gemini_api_key'));
            if ($key !== '') {
                $setting->chatbot_gemini_api_key = $key;
            }
        }

        if ($request->boolean('remove_chatbot_avatar')) {
            if ($setting->chatbot_avatar_path) {
                Storage::disk('public')->delete($setting->chatbot_avatar_path);
            }
            $setting->chatbot_avatar_path = null;
        }
        if ($request->hasFile('chatbot_avatar')) {
            if ($setting->chatbot_avatar_path) {
                Storage::disk('public')->delete($setting->chatbot_avatar_path);
            }
            $setting->chatbot_avatar_path = $request->file('chatbot_avatar')->store('chatbot', 'public');
        }
        if ($request->boolean('remove_chatbot_training_file')) {
            if ($setting->chatbot_training_file_path) {
                Storage::disk('public')->delete($setting->chatbot_training_file_path);
            }
            $setting->chatbot_training_file_path = null;
        }
        if ($request->hasFile('chatbot_training_file')) {
            if ($setting->chatbot_training_file_path) {
                Storage::disk('public')->delete($setting->chatbot_training_file_path);
            }
            $setting->chatbot_training_file_path = $request->file('chatbot_training_file')->store('chatbot-training', 'public');
        }

        $setting->home_facilities_title = $request->input('home_facilities_title');
        $setting->home_facilities_items = $request->input('home_facilities_items');
        $setting->home_location_title = $request->input('home_location_title');
        $setting->home_location_description = $request->input('home_location_description');
        $setting->home_location_map_url = $request->input('home_location_map_url');
        $setting->home_location_distances = $request->input('home_location_distances');
        $setting->home_location_cta_label = $request->input('home_location_cta_label');
        $setting->home_location_cta_link = $request->input('home_location_cta_link');
        $setting->home_offers_title = $request->input('home_offers_title');
        $setting->home_offers_items = $request->input('home_offers_items');
        $setting->home_testimonials_title = $request->input('home_testimonials_title');
        $setting->home_testimonials_items = $request->input('home_testimonials_items');
        $setting->home_faq_title = $request->input('home_faq_title');
        $setting->home_faq_items = $request->input('home_faq_items');

        $setting->social_enabled  = $request->boolean('social_enabled');
        $setting->social_facebook = $request->input('social_facebook') ?: null;
        $setting->social_zalo     = $request->input('social_zalo')     ?: null;
        $setting->social_phone    = $request->input('social_phone')    ?: null;
        $setting->social_instagram = $request->input('social_instagram') ?: null;

        $setting->policy_check_in_start = $request->input('policy_check_in_start');
        $setting->policy_check_in_end = $request->input('policy_check_in_end');
        $setting->policy_check_out_start = $request->input('policy_check_out_start');
        $setting->policy_check_out_end = $request->input('policy_check_out_end');
        $setting->no_show_cutoff_time = $request->input('no_show_cutoff_time');
        $setting->check_time_grace_minutes = (int) $request->input('check_time_grace_minutes');
        $setting->extra_hour_price = $request->input('extra_hour_price');

        $setting->save();
        SiteSetting::forgetInstance();

        return redirect()->route('admin.settings.edit')->with('status', __('Đã lưu cài đặt trang web.'));
    }

    private function backgroundUploadRules(): array
    {
        return [
            'nullable',
            function (string $attribute, mixed $value, Closure $fail): void {
                if ($value === null || $value === '') {
                    return;
                }
                if (! $value instanceof UploadedFile) {
                    $fail(__('Tệp tải lên không hợp lệ.'));

                    return;
                }
                if (! $value->isValid()) {
                    $detail = $value->getErrorMessage();
                    $hint = ' '.__('(XAMPP/PHP: tăng upload_max_filesize và post_max_size trong php.ini — ví dụ 64M — rồi khởi động lại Apache; hoặc dùng file nhỏ hơn giới hạn hiện tại.)');
                    $fail($detail !== '' ? $detail.$hint : __('Không thể tải tệp lên.').$hint);

                    return;
                }

                $maxBytes = 20 * 1024 * 1024;
                if ($value->getSize() > $maxBytes) {
                    $fail(__('Tệp không được vượt quá 20 MB.'));

                    return;
                }

                $mime = strtolower((string) $value->getMimeType());
                $ext = strtolower($value->getClientOriginalExtension());

                $blockedExtensions = [
                    'php', 'phtml', 'php3', 'php4', 'php5', 'phar', 'exe', 'sh', 'bash', 'bat', 'cmd', 'com', 'cgi',
                    'pl', 'asp', 'aspx', 'htaccess', 'htpasswd', 'dll', 'msi', 'scr', 'vbs', 'wsf', 'jar',
                ];
                if (in_array($ext, $blockedExtensions, true)) {
                    $fail(__('Loại tệp này không được phép tải lên.'));

                    return;
                }

                $blockedMimeFragments = [
                    'php', 'x-httpd-php', 'perl', 'cgi', 'x-csh', 'x-sh', 'x-msdownload', 'x-dosexec',
                    'x-executable', 'x-msdos-program', 'javascript', 'x-javascript',
                ];
                foreach ($blockedMimeFragments as $fragment) {
                    if ($fragment !== '' && str_contains($mime, $fragment)) {
                        $fail(__('Loại tệp này không được phép tải lên.'));

                        return;
                    }
                }
            },
        ];
    }

    private function chatbotTrainingUploadRules(): array
    {
        return [
            'nullable',
            function (string $attribute, mixed $value, Closure $fail): void {
                if ($value === null || $value === '') {
                    return;
                }
                if (! $value instanceof UploadedFile) {
                    $fail(__('Tệp tải lên không hợp lệ.'));

                    return;
                }
                if (! $value->isValid()) {
                    $detail = $value->getErrorMessage();
                    $hint = ' '.__('(XAMPP/PHP: tăng upload_max_filesize và post_max_size trong php.ini — ví dụ 64M — rồi khởi động lại Apache; hoặc dùng file nhỏ hơn giới hạn hiện tại.)');
                    $fail($detail !== '' ? $detail.$hint : __('Không thể tải tệp lên.').$hint);

                    return;
                }

                $maxBytes = 20 * 1024 * 1024;
                if ($value->getSize() > $maxBytes) {
                    $fail(__('Tệp không được vượt quá 20 MB.'));

                    return;
                }

                $ext = strtolower($value->getClientOriginalExtension());
                if (! in_array($ext, ['txt'], true)) {
                    $fail(__('Chỉ chấp nhận tệp .txt để huấn luyện chatbot.'));

                    return;
                }

                $mime = strtolower((string) $value->getMimeType());
                $allowedMimes = [
                    'text/plain',
                    'text/markdown',
                    'application/octet-stream',
                ];
                if ($mime !== '' && ! in_array($mime, $allowedMimes, true)) {
                    $fail(__('Tệp huấn luyện phải là văn bản thuần (.txt).'));
                }
            },
        ];
    }
}

<x-hotel-layout>
    <x-slot name="header">{{ __('PHP (upload) — kiểm tra môi trường web') }}</x-slot>

    <div class="max-w-2xl space-y-4 rounded-2xl border border-gray-200 bg-white p-6 text-sm text-gray-700 shadow-sm">
        <p class="text-gray-800">{{ __('Đây là giá trị PHP thực sự khi site chạy qua trình duyệt (Apache). Nếu bạn đã sửa php.ini mà số vẫn là 2M, thường là đang sửa nhầm file hoặc chưa khởi động lại Apache.') }}</p>

        <dl class="space-y-3 rounded-2xl border border-gray-200 bg-gray-50 p-4 font-mono text-xs sm:text-sm">
            <div>
                <dt class="text-gray-600">{{ __('File php.ini đang được nạp') }}</dt>
                <dd class="mt-1 break-all font-medium text-amber-800">{{ $ini }}</dd>
            </div>
            @if ($scanned)
                <div>
                    <dt class="text-gray-600">{{ __('Thư mục .ini bổ sung (scanned)') }}</dt>
                    <dd class="mt-1 break-all text-gray-900">{{ $scanned }}</dd>
                </div>
            @endif
            <div>
                <dt class="text-gray-600">upload_max_filesize</dt>
                <dd class="mt-1 font-semibold text-emerald-700">{{ $upload }}</dd>
            </div>
            <div>
                <dt class="text-gray-600">post_max_size</dt>
                <dd class="mt-1 font-semibold text-emerald-700">{{ $post }}</dd>
            </div>
            <div>
                <dt class="text-gray-600">max_file_uploads</dt>
                <dd class="mt-1 text-gray-900">{{ $maxFileUploads }}</dd>
            </div>
        </dl>

        <div class="rounded-xl border border-purple-200 bg-purple-50 p-4 text-gray-800">
            <p class="font-semibold text-purple-900">{{ __('XAMPP: mở đúng file') }}</p>
            <p class="mt-2">{{ __('Trong XAMPP Control Panel → Apache → Config → PHP (php.ini) — đây là file Apache đang dùng. So sánh đường dẫn với dòng phía trên.') }}</p>
            <p class="mt-2">{{ __('Sau khi sửa: Save, rồi Stop Apache → Start (hoặc Restart).') }}</p>
            <p class="mt-2">{{ __('Trong php.ini, tìm mọi dòng upload_max_filesize / post_max_size — chỉ giữ một dòng mỗi loại (xóa hoặc comment dòng trùng).') }}</p>
        </div>

        <p>
            <a href="{{ route('admin.settings.edit') }}" class="font-medium text-purple-700 hover:text-purple-900">{{ __('← Quay lại Cài đặt website') }}</a>
        </p>
    </div>
</x-hotel-layout>

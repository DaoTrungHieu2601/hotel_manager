<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PhpEnvironmentController extends Controller
{
    /**
     * Hiển thị cấu hình PHP thực tế khi chạy qua web (Apache) — có thể khác CLI và khác file bạn mở nhầm.
     */
    public function __invoke(): View
    {
        $ini = php_ini_loaded_file() ?: '(không xác định)';
        $scanned = php_ini_scanned_files();
        $upload = ini_get('upload_max_filesize');
        $post = ini_get('post_max_size');
        $maxFileUploads = ini_get('max_file_uploads');

        return view('admin.php-environment', compact('ini', 'scanned', 'upload', 'post', 'maxFileUploads'));
    }
}

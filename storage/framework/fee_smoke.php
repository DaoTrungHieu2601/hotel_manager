<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $b = App\Models\Booking::query()->whereNotNull('guest_planned_check_in')->latest('id')->first();
    if (! $b) {
        echo "NO_BOOKING_WITH_PLANNED_TIME\n";
        exit(0);
    }
    $svc = app(App\Services\CheckoutTotalsService::class);
    [$fee, $hours] = $svc->previewCheckInEarlyFee($b, now());
    echo json_encode([
        'booking_id' => $b->id,
        'planned_in' => $b->guest_planned_check_in,
        'preview_fee' => $fee,
        'preview_hours' => $hours,
    ], JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Throwable $e) {
    echo 'ERR: '.get_class($e).'|'.$e->getMessage().PHP_EOL;
    exit(1);
}

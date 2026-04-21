Gói trích xuất module Lễ tân (Reception) — copy từ project Laravel gốc.

Cấu trúc thư mục giống repo chính để dễ so sánh / merge lại.

Gồm 10 file:
  app/Http/Controllers/Reception/*.php (5 controller)
  resources/views/reception/...
  resources/views/layouts/hotel.blade.php

Lưu ý: Đây không phải project chạy độc lập. Cần thêm routes (web.php nhóm reception),
Models (Booking, Room, ...), middleware role, component x-hotel-layout, v.v. trong repo đầy đủ.

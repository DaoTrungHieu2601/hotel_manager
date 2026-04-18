<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class HotelLayout extends Component
{
    public function __construct(
        public string $title = ''
    ) {}

    public function render(): View
    {
        return view('layouts.hotel');
    }
}

<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AuthSplitLayout extends Component
{
    public function render(): View
    {
        return view('layouts.auth-split');
    }
}

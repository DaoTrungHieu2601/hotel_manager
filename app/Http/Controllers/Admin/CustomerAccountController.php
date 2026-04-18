<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class CustomerAccountController extends Controller
{
    public function index(): View
    {
        $customers = User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }
}


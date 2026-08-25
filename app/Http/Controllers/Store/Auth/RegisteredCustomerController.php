<?php

namespace App\Http\Controllers\Store\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\Auth\RegisterCustomerRequest;
use App\Models\Customer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisteredCustomerController extends Controller
{
    public function store(RegisterCustomerRequest $request): RedirectResponse
    {
        $customer = Customer::query()->create($request->validated());

        event(new Registered($customer));
        Auth::guard('store')->login($customer);
        $request->session()->regenerate();

        return to_route('store.home');
    }
}

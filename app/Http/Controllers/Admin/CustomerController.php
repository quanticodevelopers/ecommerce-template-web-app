<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CustomerResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of customer users.
     */
    public function index(Request $request): Response
    {
        $customers = User::query()
            ->select(['id', 'name', 'last_name', 'email', 'phone', 'document_type', 'document_number', 'is_active', 'created_at', 'role'])
            ->where('role', UserRole::CUSTOMER->value)
            ->latest('created_at')
            ->get();

        return Inertia::render('admin/customers/index', [
            'customers' => CustomerResource::collection($customers)->resolve(),
        ]);
    }
}

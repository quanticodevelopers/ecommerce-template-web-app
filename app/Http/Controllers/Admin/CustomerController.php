<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CustomerResource;
use App\Models\Customer;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of customer users.
     */
    public function index(): Response
    {
        $customers = Customer::query()
            ->select(['id', 'name', 'last_name', 'email', 'phone', 'document_type', 'document_number', 'created_at'])
            ->latest('created_at')
            ->get();

        return Inertia::render('admin/customers/index', [
            'customers' => CustomerResource::collection($customers)->resolve(),
        ]);
    }
}

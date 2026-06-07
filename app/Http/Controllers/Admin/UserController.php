<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of administrator users.
     */
    public function index(): Response
    {
        $users = User::query()
            ->select(['id', 'name', 'last_name', 'email', 'phone', 'document_type', 'document_number', 'created_at', 'role'])
            ->where('role', UserRole::ADMIN->value)
            ->latest('created_at')
            ->get();

        return Inertia::render('admin/users/index', [
            'users' => UserResource::collection($users)->resolve(),
        ]);
    }
}

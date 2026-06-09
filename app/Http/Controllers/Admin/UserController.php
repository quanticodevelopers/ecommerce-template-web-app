<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserDocumentType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConfirmAdminPasswordRequest;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
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
            'document_type_options' => UserDocumentType::options(),
            'created_user_credentials' => session('created_user_credentials'),
        ]);
    }

    /**
     * Store a newly created administrator user.
     */
    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $generatedPassword = Str::random(18);

        $user = User::query()->create([
            'document_type' => $validated['document_type'],
            'document_number' => $validated['document_number'],
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $generatedPassword,
            'role' => UserRole::ADMIN,
        ]);

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Usuario administrador creado correctamente.',
        ]);

        return to_route('admin.users.index')->with('created_user_credentials', [
            'name' => trim($user->name.' '.$user->last_name),
            'email' => $user->email,
            'password' => $generatedPassword,
        ]);
    }

    /**
     * Reset an administrator user's password.
     */
    public function resetPassword(ConfirmAdminPasswordRequest $request, User $user): RedirectResponse
    {
        if ($user->role !== UserRole::ADMIN) {
            abort(404);
        }

        $generatedPassword = Str::random(18);

        $user->forceFill([
            'password' => $generatedPassword,
        ])->save();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Contrasena de usuario administrador restablecida correctamente.',
        ]);

        return to_route('admin.users.index')->with('created_user_credentials', [
            'name' => trim($user->name.' '.$user->last_name),
            'email' => $user->email,
            'password' => $generatedPassword,
        ]);
    }
}

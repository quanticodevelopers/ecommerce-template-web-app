<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserDocumentType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeactivateAdminUserRequest;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of administrator users.
     */
    public function index(Request $request): Response
    {
        $users = User::query()
            ->select(['id', 'name', 'last_name', 'email', 'phone', 'document_type', 'document_number', 'is_active', 'created_at', 'role'])
            ->where('role', UserRole::ADMIN->value)
            ->latest('created_at')
            ->get();

        return Inertia::render('admin/users/index', [
            'users' => UserResource::collection($users)->resolve(),
            'current_user_id' => $request->user()?->id,
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
            'is_active' => true,
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
     * Deactivate an administrator user.
     */
    public function deactivate(DeactivateAdminUserRequest $request, User $user): RedirectResponse
    {
        if ($user->role !== UserRole::ADMIN) {
            abort(404);
        }

        if ($request->user()?->is($user)) {
            return back()->withErrors([
                'deactivate_user' => 'No puedes desactivar tu usuario actual.',
            ]);
        }

        $user->forceFill([
            'is_active' => false,
        ])->save();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Usuario administrador desactivado correctamente.',
        ]);

        return to_route('admin.users.index');
    }

    /**
     * Reactivate an administrator user.
     */
    public function reactivate(DeactivateAdminUserRequest $request, User $user): RedirectResponse
    {
        if ($user->role !== UserRole::ADMIN) {
            abort(404);
        }

        $user->forceFill([
            'is_active' => true,
        ])->save();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Usuario administrador activado correctamente.',
        ]);

        return to_route('admin.users.index');
    }
}

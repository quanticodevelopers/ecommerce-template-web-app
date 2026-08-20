<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Users\CreateAdminUserAction;
use App\Actions\Users\ResetAdminUserPasswordAction;
use App\Enums\UserDocumentType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConfirmAdminPasswordRequest;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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
    public function store(StoreAdminUserRequest $request, CreateAdminUserAction $createAdminUser): RedirectResponse
    {
        ['user' => $user, 'password' => $generatedPassword] = $createAdminUser->handle($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Usuario administrador creado correctamente.',
        ]);

        return to_route('admin.users.index')->with(
            'created_user_credentials',
            $this->credentialsFor($user, $generatedPassword),
        );
    }

    /**
     * Reset an administrator user's password.
     */
    public function resetPassword(
        ConfirmAdminPasswordRequest $request,
        User $user,
        ResetAdminUserPasswordAction $resetAdminUserPassword,
    ): RedirectResponse {
        $generatedPassword = $resetAdminUserPassword->handle($user);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Contraseña de usuario administrador restablecida correctamente.',
        ]);

        return to_route('admin.users.index')->with(
            'created_user_credentials',
            $this->credentialsFor($user, $generatedPassword),
        );
    }

    /** @return array{name: string, email: string, password: string} */
    private function credentialsFor(User $user, string $password): array
    {
        return [
            'name' => trim($user->name.' '.$user->last_name),
            'email' => $user->email,
            'password' => $password,
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Users\CreateAdminUserAction;
use App\Actions\Users\ResetAdminUserPasswordAction;
use App\Enums\AdministratorRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConfirmAdminPasswordRequest;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Resources\Admin\AdministratorResource;
use App\Models\Administrator;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdministratorController extends Controller
{
    /**
     * Display a listing of administrators.
     */
    public function index(): Response
    {
        $administrators = Administrator::query()
            ->select(['id', 'name', 'last_name', 'email', 'phone', 'created_at', 'role'])
            ->where('role', AdministratorRole::ADMIN->value)
            ->latest('created_at')
            ->get();

        return Inertia::render('admin/admins/index', [
            'admins' => AdministratorResource::collection($administrators)->resolve(),
            'created_administrator_credentials' => session('created_administrator_credentials'),
        ]);
    }

    /**
     * Store a newly created administrator user.
     */
    public function store(StoreAdminUserRequest $request, CreateAdminUserAction $createAdminUser): RedirectResponse
    {
        ['administrator' => $administrator, 'password' => $generatedPassword] = $createAdminUser->handle($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Usuario administrador creado correctamente.',
        ]);

        return to_route('admin.admins.index')->with(
            'created_administrator_credentials',
            $this->credentialsFor($administrator, $generatedPassword),
        );
    }

    /**
     * Reset an administrator user's password.
     */
    public function resetPassword(
        ConfirmAdminPasswordRequest $request,
        Administrator $administrator,
        ResetAdminUserPasswordAction $resetAdminUserPassword,
    ): RedirectResponse {
        $generatedPassword = $resetAdminUserPassword->handle($administrator);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Contraseña de usuario administrador restablecida correctamente.',
        ]);

        return to_route('admin.admins.index')->with(
            'created_administrator_credentials',
            $this->credentialsFor($administrator, $generatedPassword),
        );
    }

    /** @return array{name: string, email: string, password: string} */
    private function credentialsFor(Administrator $administrator, string $password): array
    {
        return [
            'name' => trim($administrator->name.' '.$administrator->last_name),
            'email' => $administrator->email,
            'password' => $password,
        ];
    }
}

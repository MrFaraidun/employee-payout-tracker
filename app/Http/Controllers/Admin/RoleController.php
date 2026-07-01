<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): Response
    {
        if (Auth::user()->role !== UserRoleEnum::SuperAdmin && Auth::user()->role !== UserRoleEnum::Admin && !Auth::user()->can('view roles')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'users_count' => \App\Models\User::role($role->name)->count(),
                'permissions_count' => $role->permissions()->count(),
                'updated_at' => $role->updated_at?->format('Y-m-d H:i:s') ?? 'N/A',
            ];
        });

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
        ]);
    }

    public function create(): Response
    {
        if (Auth::user()->role !== UserRoleEnum::SuperAdmin && Auth::user()->role !== UserRoleEnum::Admin && !Auth::user()->can('create roles')) {
            abort(403, 'Unauthorized action.');
        }

        $permissions = Permission::all()->map(function ($perm) {
            return [
                'id' => $perm->id,
                'name' => $perm->name,
            ];
        });

        return Inertia::render('Roles/Edit', [
            'role' => null,
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (Auth::user()->role !== UserRoleEnum::SuperAdmin && Auth::user()->role !== UserRoleEnum::Admin && !Auth::user()->can('create roles')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        if ($request->filled('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): Response
    {
        if (Auth::user()->role !== UserRoleEnum::SuperAdmin && Auth::user()->role !== UserRoleEnum::Admin && !Auth::user()->can('update roles')) {
            abort(403, 'Unauthorized action.');
        }

        $permissions = Permission::all()->map(function ($perm) {
            return [
                'id' => $perm->id,
                'name' => $perm->name,
            ];
        });

        return Inertia::render('Roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
            ],
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if (Auth::user()->role !== UserRoleEnum::SuperAdmin && Auth::user()->role !== UserRoleEnum::Admin && !Auth::user()->can('update roles')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (Auth::user()->role !== UserRoleEnum::SuperAdmin && Auth::user()->role !== UserRoleEnum::Admin && !Auth::user()->can('delete roles')) {
            abort(403, 'Unauthorized action.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}

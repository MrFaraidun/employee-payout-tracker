<?php

namespace App\Http\Controllers\Auth;

use App\Enums\OrganizationStatusEnum;
use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'organization_name' => 'required|string|max:255',
        ]);

        $user = DB::transaction(function () use ($request) {
            // 1. Create Organization
            $org = Organization::create([
                'name' => $request->organization_name,
                'status' => OrganizationStatusEnum::Active,
            ]);

            // 2. Create User
            $user = User::create([
                'organization_id' => $org->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => UserRoleEnum::Admin,
                'status' => UserStatusEnum::Active,
            ]);

            // 3. Assign Spatie Admin Role
            $user->assignRole(UserRoleEnum::Admin->value);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

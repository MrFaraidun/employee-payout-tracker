<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\UserResource;
use App\Models\Organization;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(protected UserService $service)
    {
        //
    }

    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', User::class);

        $currentUser = Auth::user();
        $query = User::with('organization');

        if ($currentUser->role !== UserRoleEnum::SuperAdmin) {
            $query->where('organization_id', $currentUser->organization_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        $organizations = $currentUser->role === UserRoleEnum::SuperAdmin
            ? Organization::all()
            : Organization::where('id', $currentUser->organization_id)->get();

        return Inertia::render('Admins/Index', [
            'users' => UserResource::collection($users),
            'organizations' => OrganizationResource::collection($organizations),
            'roles' => \Spatie\Permission\Models\Role::pluck('name')->toArray(),
            'statuses' => UserStatusEnum::values(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $data = $request->validated();
        $currentUser = Auth::user();

        // Enforce organization isolation if not SuperAdmin
        if ($currentUser->role !== UserRoleEnum::SuperAdmin) {
            $data['organization_id'] = $currentUser->organization_id;
            
            // Admins cannot create SuperAdmins
            if ($data['role'] === UserRoleEnum::SuperAdmin->value || $data['role'] === UserRoleEnum::SuperAdmin) {
                $data['role'] = UserRoleEnum::Accountant;
            }
        }

        $this->service->create($data);

        return redirect()->route('users.index');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $data = $request->validated();
        $currentUser = Auth::user();

        // Enforce organization isolation if not SuperAdmin
        if ($currentUser->role !== UserRoleEnum::SuperAdmin) {
            $data['organization_id'] = $currentUser->organization_id;
            
            // Admins cannot elevate to SuperAdmin
            if ($data['role'] === UserRoleEnum::SuperAdmin->value || $data['role'] === UserRoleEnum::SuperAdmin) {
                $data['role'] = $user->role;
            }
        }

        $this->service->update($user, $data);

        return redirect()->route('users.index');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        $this->service->delete($user);

        return redirect()->route('users.index');
    }
}

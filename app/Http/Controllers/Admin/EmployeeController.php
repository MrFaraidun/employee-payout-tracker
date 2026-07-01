<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EmployeeStatusEnum;
use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\OrganizationResource;
use App\Models\Employee;
use App\Models\Organization;
use App\Services\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function __construct(protected EmployeeService $service)
    {
        //
    }

    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Employee::class);

        $currentUser = Auth::user();
        $query = Employee::with('organization');

        if ($currentUser->role !== UserRoleEnum::SuperAdmin) {
            $query->where('organization_id', $currentUser->organization_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('employee_code', 'like', '%' . $request->search . '%')
                  ->orWhere('department', 'like', '%' . $request->search . '%')
                  ->orWhere('position', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $employees = $query->latest()->paginate(10)->withQueryString();

        $organizations = $currentUser->role === UserRoleEnum::SuperAdmin
            ? Organization::all()
            : Organization::where('id', $currentUser->organization_id)->get();

        return Inertia::render('Employees/Index', [
            'employees' => EmployeeResource::collection($employees),
            'organizations' => OrganizationResource::collection($organizations),
            'statuses' => EmployeeStatusEnum::values(),
            'filters' => $request->only(['search', 'status', 'department']),
        ]);
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        Gate::authorize('create', Employee::class);

        $data = $request->validated();
        $currentUser = Auth::user();

        // Enforce organization isolation if not SuperAdmin
        if ($currentUser->role !== UserRoleEnum::SuperAdmin) {
            $data['organization_id'] = $currentUser->organization_id;
        }

        $this->service->create($data);

        return redirect()->route('employees.index');
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        Gate::authorize('update', $employee);

        $data = $request->validated();
        $currentUser = Auth::user();

        // Enforce organization isolation if not SuperAdmin
        if ($currentUser->role !== UserRoleEnum::SuperAdmin) {
            $data['organization_id'] = $currentUser->organization_id;
        }

        $this->service->update($employee, $data);

        return redirect()->route('employees.index');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        Gate::authorize('delete', $employee);

        $this->service->delete($employee);

        return redirect()->route('employees.index');
    }
}

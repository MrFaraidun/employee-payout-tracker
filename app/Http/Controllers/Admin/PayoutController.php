<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PayoutStatusEnum;
use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayoutRequest;
use App\Http\Requests\UpdatePayoutRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\PayoutResource;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Payout;
use App\Services\PayoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PayoutController extends Controller
{
    public function __construct(protected PayoutService $service)
    {
        //
    }

    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Payout::class);

        $currentUser = Auth::user();
        $orgId = $currentUser->organization_id;

        $query = Payout::with(['employee', 'organization', 'creator', 'updater']);

        if ($currentUser->role !== UserRoleEnum::SuperAdmin) {
            $query->where('organization_id', $orgId);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('task', 'like', '%' . $search . '%')
                  ->orWhereHas('employee', function ($eq) use ($search) {
                      $eq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payouts = $query->latest()->paginate(10)->withQueryString();

        // Load employees list for filters and creation forms
        $employeesQuery = Employee::query();
        if ($currentUser->role !== UserRoleEnum::SuperAdmin) {
            $employeesQuery->where('organization_id', $orgId);
        }
        $employees = $employeesQuery->orderBy('name')->get();

        $organizations = $currentUser->role === UserRoleEnum::SuperAdmin
            ? Organization::all()
            : Organization::where('id', $orgId)->get();

        return Inertia::render('Payouts/Index', [
            'payouts' => PayoutResource::collection($payouts),
            'employees' => EmployeeResource::collection($employees),
            'organizations' => OrganizationResource::collection($organizations),
            'statuses' => PayoutStatusEnum::values(),
            'filters' => $request->only(['search', 'status', 'employee_id', 'date_from', 'date_to']),
        ]);
    }

    public function store(StorePayoutRequest $request): RedirectResponse
    {
        Gate::authorize('create', Payout::class);

        $data = $request->validated();
        $currentUser = Auth::user();

        // Enforce organization isolation if not SuperAdmin
        if ($currentUser->role !== UserRoleEnum::SuperAdmin) {
            $data['organization_id'] = $currentUser->organization_id;
        }

        $this->service->create($data);

        return redirect()->route('payouts.index');
    }

    public function update(UpdatePayoutRequest $request, Payout $payout): RedirectResponse
    {
        Gate::authorize('update', $payout);

        $data = $request->validated();
        $currentUser = Auth::user();

        // Enforce organization isolation if not SuperAdmin
        if ($currentUser->role !== UserRoleEnum::SuperAdmin) {
            $data['organization_id'] = $currentUser->organization_id;
        }

        $this->service->update($payout, $data);

        return redirect()->route('payouts.index');
    }

    public function destroy(Payout $payout): RedirectResponse
    {
        Gate::authorize('delete', $payout);

        $this->service->delete($payout);

        return redirect()->route('payouts.index');
    }
}

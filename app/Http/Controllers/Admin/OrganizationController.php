<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use App\Services\OrganizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller
{
    public function __construct(protected OrganizationService $service)
    {
        //
    }

    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Organization::class);

        $query = Organization::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $organizations = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Organizations/Index', [
            'organizations' => OrganizationResource::collection($organizations),
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        Gate::authorize('create', Organization::class);

        $this->service->create($request->validated());

        return redirect()->route('organizations.index');
    }

    public function update(StoreOrganizationRequest $request, Organization $organization): RedirectResponse
    {
        Gate::authorize('update', $organization);

        $this->service->update($organization, $request->validated());

        return redirect()->route('organizations.index');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        Gate::authorize('delete', $organization);

        $this->service->delete($organization);

        return redirect()->route('organizations.index');
    }
}

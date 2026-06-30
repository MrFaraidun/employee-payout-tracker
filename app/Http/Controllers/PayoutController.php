<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payout;
use App\Http\Requests\StorePayoutRequest;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class PayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $payouts = Payout::with('employee')
            ->latest()
            ->get();

        $employees = Employee::orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Payouts/Index', [
            'payouts' => $payouts,
            'employees' => $employees,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePayoutRequest $request): RedirectResponse
    {
        Payout::create($request->validated());

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePayoutRequest $request, Payout $payout): RedirectResponse
    {
        $payout->update($request->validated());

        return redirect()->back();
    }


    /**
     * Update the status of a payout.
     */
    public function updateStatus(Payout $payout): RedirectResponse
    {
        $request = request();
        $request->validate([
            'status' => 'required|in:pending,processing,completed',
        ]);

        $payout->update(['status' => $request->status]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payout $payout): RedirectResponse
    {
        $payout->delete();

        return redirect()->back();
    }
}

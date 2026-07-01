<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payout;
use App\Enums\PayoutStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();
        $orgId = $user->role === \App\Enums\UserRoleEnum::SuperAdmin ? null : $user->organization_id;
        $cacheKey = $orgId ? "dashboard_stats_org_{$orgId}" : 'dashboard_stats_global';

        // Cache dashboard counts for 5 minutes
        $stats = Cache::remember($cacheKey, 300, function () use ($orgId) {
            $payoutQuery = Payout::query();
            $employeeQuery = Employee::query();

            if ($orgId) {
                $payoutQuery->where('organization_id', $orgId);
                $employeeQuery->where('organization_id', $orgId);
            }

            return [
                'total_amount_iqd' => (int) $payoutQuery->clone()->where('status', PayoutStatusEnum::Completed)->sum('amount_iqd'),
                'pending_count' => $payoutQuery->clone()->where('status', PayoutStatusEnum::Pending)->count(),
                'processing_count' => $payoutQuery->clone()->where('status', PayoutStatusEnum::Processing)->count(),
                'completed_count' => $payoutQuery->clone()->where('status', PayoutStatusEnum::Completed)->count(),
                'total_employees' => $employeeQuery->count(),
            ];
        });

        // Fetch recent payouts (not cached as they change rapidly)
        $payoutsQuery = Payout::with(['employee', 'creator'])
            ->latest();

        $employeesQuery = Employee::query()
            ->latest();

        if ($orgId) {
            $payoutsQuery->where('organization_id', $orgId);
            $employeesQuery->where('organization_id', $orgId);
        }

        $recentPayouts = $payoutsQuery->limit(5)->get();
        $recentEmployees = $employeesQuery->limit(5)->get();

        $chartData = Payout::query();
        if ($orgId) {
            $chartData->where('organization_id', $orgId);
        }
        $chartData = $chartData->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->status->value ?? $item->status => $item->count])
            ->toArray();

        $auditLogsQuery = \App\Models\AuditLog::with('user')->latest();
        if ($orgId) {
            $auditLogsQuery->whereHas('user', function ($q) use ($orgId) {
                $q->where('organization_id', $orgId);
            });
        }
        $recentAuditLogs = $auditLogsQuery->limit(5)->get();

        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
            'recentPayouts' => \App\Http\Resources\PayoutResource::collection($recentPayouts),
            'recentEmployees' => \App\Http\Resources\EmployeeResource::collection($recentEmployees),
            'chartData' => $chartData,
            'recentAuditLogs' => $recentAuditLogs,
        ]);
    }
}

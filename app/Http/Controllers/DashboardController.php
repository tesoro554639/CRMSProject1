<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\FollowUp;
use App\Models\Lead;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        if ($user->isAdmin() || $user->isManager()) {
            return $this->adminManagerDashboard();
        }

        return $this->salesDashboard();
    }

    protected function adminManagerDashboard(): View
    {
        $totalCustomers = Customer::count();
        $activeLeads = Lead::whereIn('status', ['new', 'contacted', 'qualified', 'proposal_sent', 'negotiation'])->count();
        $completedFollowUps = FollowUp::where('status', 'completed')->count();
        $overdueFollowUps = FollowUp::where('status', 'pending')
            ->where('due_date', '<', now()->toDateString())
            ->count();

        $leadStatusCounts = Lead::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $completedFollowUpsCount = FollowUp::where('status', 'completed')->count();
        $pendingFollowUps = FollowUp::where('status', 'pending')->count();
        $overdueFollowUpsCount = FollowUp::where('status', 'pending')
            ->where('due_date', '<', now()->toDateString())
            ->count();

        $upcomingFollowUps = FollowUp::with(['lead', 'user'])
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $recentActivities = Activity::with(['user', 'lead', 'customer'])
            ->orderBy('activity_date', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.admin', compact(
            'totalCustomers',
            'activeLeads',
            'completedFollowUps',
            'overdueFollowUps',
            'leadStatusCounts',
            'completedFollowUpsCount',
            'pendingFollowUps',
            'overdueFollowUpsCount',
            'upcomingFollowUps',
            'recentActivities'
        ));
    }

    protected function salesDashboard(): View
    {
        $userId = auth()->id();

        $totalCustomers = Customer::where('assigned_user_id', $userId)->count();
        $activeLeads = Lead::where('assigned_user_id', $userId)
            ->whereIn('status', ['new', 'contacted', 'qualified', 'proposal_sent', 'negotiation'])
            ->count();
        $completedFollowUps = FollowUp::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();
        $overdueFollowUps = FollowUp::where('user_id', $userId)
            ->where('status', 'pending')
            ->where('due_date', '<', now()->toDateString())
            ->count();

        $leadStatusCounts = Lead::where('assigned_user_id', $userId)
            ->select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $completedFollowUpsCount = FollowUp::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();
        $pendingFollowUps = FollowUp::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();
        $overdueFollowUpsCount = FollowUp::where('user_id', $userId)
            ->where('status', 'pending')
            ->where('due_date', '<', now()->toDateString())
            ->count();

        $upcomingFollowUps = FollowUp::with(['lead', 'user'])
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $recentActivities = Activity::with(['user', 'lead', 'customer'])
            ->where('user_id', $userId)
            ->orderBy('activity_date', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.sales', compact(
            'totalCustomers',
            'activeLeads',
            'completedFollowUps',
            'overdueFollowUps',
            'leadStatusCounts',
            'completedFollowUpsCount',
            'pendingFollowUps',
            'overdueFollowUpsCount',
            'upcomingFollowUps',
            'recentActivities'
        ));
    }
}

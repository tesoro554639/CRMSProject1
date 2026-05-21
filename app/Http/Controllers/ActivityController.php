<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $query = Activity::with(['user', 'lead', 'customer']);

        if (auth()->user()->isSales()) {
            $query->where('user_id', auth()->id());
        }

        if ($search = $request->get('search')) {
            $query->where('description', 'like', "%{$search}%");
        }

        if ($type = $request->get('activity_type')) {
            $query->where('activity_type', $type);
        }

        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }

        $activities = $query->orderBy('activity_date', 'desc')->paginate(20);
        $totalActivities = Activity::count();

        $users = User::where('role', 'sales')->get();

        return view('activities.index', compact('activities', 'totalActivities', 'users'));
    }

    public function create(): View
    {
        $customers = Customer::all();
        $leads = Lead::all();
        $users = User::where('role', 'sales')->get();

        return view('activities.create', compact('customers', 'leads', 'users'));
    }

    public function store(StoreActivityRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        Activity::create($data);

        return redirect()->route('activities.index')
            ->with('success', 'Activity logged successfully.');
    }

    public function edit(Activity $activity): View
    {
        if (auth()->user()->isSales() && $activity->user_id !== auth()->id()) {
            return redirect()->route('activities.index')
                ->with('error', 'You do not have permission to edit this activity.');
        }

        $customers = Customer::all();
        $leads = Lead::all();
        $users = User::where('role', 'sales')->get();

        return view('activities.edit', compact('activity', 'customers', 'leads', 'users'));
    }

    public function update(StoreActivityRequest $request, Activity $activity)
    {
        if (auth()->user()->isSales() && $activity->user_id !== auth()->id()) {
            return redirect()->route('activities.index')
                ->with('error', 'You do not have permission to update this activity.');
        }

        $activity->update($request->validated());

        return redirect()->route('activities.index')
            ->with('success', 'Activity updated successfully.');
    }

    public function destroy(Activity $activity)
    {
        if (auth()->user()->isSales() && $activity->user_id !== auth()->id()) {
            return redirect()->route('activities.index')
                ->with('error', 'You do not have permission to delete this activity.');
        }

        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Activity deleted successfully.');
    }
}

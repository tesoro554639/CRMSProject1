<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFollowUpRequest;
use App\Http\Requests\UpdateFollowUpRequest;
use App\Models\Customer;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    public function index(Request $request): View
    {
        $query = FollowUp::with(['customer', 'lead', 'user']);

        if (auth()->user()->isSales()) {
            $query->where('user_id', auth()->id());
        }

        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $followUps = $query->orderBy('due_date', 'asc')->paginate(15);

        $totalFollowUps = FollowUp::count();
        $pendingFollowUps = FollowUp::where('status', 'pending')->count();
        $completedFollowUps = FollowUp::where('status', 'completed')->count();

        $customers = Customer::all();
        $leads = Lead::all();
        $users = User::where('role', 'sales')->get();

        return view('follow_ups.index', compact(
            'followUps',
            'totalFollowUps',
            'pendingFollowUps',
            'completedFollowUps',
            'customers',
            'leads',
            'users'
        ));
    }

    public function create(): View
    {
        $customers = Customer::all();
        $leads = Lead::all();
        $users = User::where('role', 'sales')->get();

        return view('follow_ups.create', compact('customers', 'leads', 'users'));
    }

    public function store(StoreFollowUpRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user_id ?? auth()->id();

        FollowUp::create($data);

        return redirect()->route('follow-ups.index')
            ->with('success', 'Follow-up created successfully.');
    }

    public function edit(FollowUp $followUp): View
    {
        $customers = Customer::all();
        $leads = Lead::all();
        $users = User::where('role', 'sales')->get();

        return view('follow_ups.edit', compact('followUp', 'customers', 'leads', 'users'));
    }

    public function update(UpdateFollowUpRequest $request, FollowUp $followUp)
    {
        if ($followUp->isCompleted() && ! auth()->user()->isAdmin()) {
            return redirect()->back()
                ->with('error', 'Completed follow-ups can only be edited by admins.');
        }

        $followUp->update($request->validated());

        return redirect()->route('follow-ups.index')
            ->with('success', 'Follow-up updated successfully.');
    }

    public function complete(FollowUp $followUp)
    {
        $followUp->update(['status' => 'completed']);

        return redirect()->back()
            ->with('success', 'Follow-up marked as completed.');
    }

    public function reopen(FollowUp $followUp)
    {
        if (! auth()->user()->isAdmin()) {
            return redirect()->back()
                ->with('error', 'Only admins can reopen follow-ups.');
        }

        $followUp->update(['status' => 'pending']);

        return redirect()->back()
            ->with('success', 'Follow-up reopened successfully.');
    }

    public function destroy(FollowUp $followUp)
    {
        if (auth()->user()->isSales() && $followUp->user_id !== auth()->id()) {
            return redirect()->route('follow-ups.index')
                ->with('error', 'You do not have permission to delete this follow-up.');
        }

        $followUp->delete();

        return redirect()->route('follow-ups.index')
            ->with('success', 'Follow-up deleted successfully.');
    }
}

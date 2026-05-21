<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadLostFormRequest;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $query = Lead::with('assignedUser');

        if (auth()->user()->isSales()) {
            $query->where('assigned_user_id', auth()->id());
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('lead_id', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }

        if ($assignedUser = $request->get('assigned_user_id')) {
            $query->where('assigned_user_id', $assignedUser);
        }

        $leads = $query->orderBy('created_at', 'desc')->paginate(15);
        $totalLeads = Lead::count();

        $salesUsers = User::where('role', 'sales')->get();

        return view('leads.index', compact('leads', 'totalLeads', 'salesUsers'));
    }

    public function kanban(Request $request): View
    {
        $query = Lead::with('assignedUser');

        if (auth()->user()->isSales()) {
            $query->where('assigned_user_id', auth()->id());
        }

        $leads = $query->orderBy('created_at', 'desc')->get();
        $salesUsers = User::where('role', 'sales')->get();

        return view('leads.kanban', compact('leads', 'salesUsers'));
    }

    public function create(): View
    {
        $salesUsers = User::where('role', 'sales')->get();

        return view('leads.create', compact('salesUsers'));
    }

    public function store(StoreLeadRequest $request)
    {
        $data = $request->validated();

        if (auth()->user()->isSales()) {
            $data['assigned_user_id'] = auth()->id();
        }

        Lead::create($data);

        return redirect()->route('leads.index')
            ->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead): View
    {
        $lead->load(['assignedUser', 'activities', 'followUps']);

        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead): View
    {
        if (auth()->user()->isSales() && $lead->assigned_user_id !== auth()->id()) {
            return redirect()->route('leads.index')
                ->with('error', 'You do not have permission to edit this lead.');
        }

        $salesUsers = User::where('role', 'sales')->get();

        return view('leads.edit', compact('lead', 'salesUsers'));
    }

    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        if (auth()->user()->isSales() && $lead->assigned_user_id !== auth()->id()) {
            return redirect()->route('leads.show', $lead)
                ->with('error', 'You do not have permission to update this lead.');
        }

        $lead->update($request->validated());

        return redirect()->route('leads.show', $lead)
            ->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        if (! auth()->user()->isAdmin()) {
            return redirect()->route('leads.index')
                ->with('error', 'Only admins can delete leads.');
        }

        $lead->delete();

        return redirect()->route('leads.index')
            ->with('success', 'Lead deleted successfully.');
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,qualified,proposal_sent,negotiation,won,lost',
        ]);

        $lead->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    public function lostForm(Lead $lead): View
    {
        return view('leads.lost-form', compact('lead'));
    }

    public function markLost(LeadLostFormRequest $request, Lead $lead)
    {
        $lead->update([
            'status' => 'lost',
            'lost_category' => $request->lost_category,
            'lost_reason' => $request->lost_reason,
            'lost_at' => now(),
        ]);

        return redirect()->route('leads.show', $lead)
            ->with('success', 'Lead marked as lost.');
    }

    public function convertToCustomer(Lead $lead)
    {
        if (! auth()->user()->isAdmin()) {
            return redirect()->back()
                ->with('error', 'Only admins can convert leads to customers.');
        }

        if (! $lead->canBeConverted()) {
            return redirect()->back()
                ->with('error', 'This lead cannot be converted.');
        }

        $customer = Customer::create([
            'first_name' => explode(' ', $lead->name)[0],
            'last_name' => implode(' ', array_slice(explode(' ', $lead->name), 1)),
            'email' => $lead->email,
            'phone' => $lead->phone,
            'company' => $lead->source,
            'status' => 'active',
            'assigned_user_id' => $lead->assigned_user_id,
            'assignment_status' => 'approved',
            'assignment_reviewed_by' => auth()->id(),
            'assignment_reviewed_at' => now(),
        ]);

        $lead->update([
            'converted_to_customer_id' => $customer->id,
            'converted_at' => now(),
        ]);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Lead converted to customer successfully.');
    }

    public function reopen(Lead $lead)
    {
        if (! $lead->isLost() || ! auth()->user()->isAdmin()) {
            return redirect()->back()
                ->with('error', 'You cannot reopen this lead.');
        }

        $lead->update([
            'status' => 'new',
            'lost_reason' => null,
            'lost_category' => null,
            'lost_at' => null,
        ]);

        return redirect()->route('leads.show', $lead)
            ->with('success', 'Lead reopened successfully.');
    }
}

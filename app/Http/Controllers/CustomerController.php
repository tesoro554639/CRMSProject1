<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::with('assignedUser');

        if (auth()->user()->isSales()) {
            $query->where('assigned_user_id', auth()->id());
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($assignmentStatus = $request->get('assignment_status')) {
            $query->where('assignment_status', $assignmentStatus);
        }

        if ($assignedUser = $request->get('assigned_user_id')) {
            $query->where('assigned_user_id', $assignedUser);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(15);

        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 'active')->count();
        $inactiveCustomers = Customer::where('status', 'inactive')->count();
        $newThisMonth = Customer::whereMonth('created_at', now()->month)->count();

        $salesUsers = User::where('role', 'sales')->get();

        return view('customers.index', compact(
            'customers',
            'totalCustomers',
            'activeCustomers',
            'inactiveCustomers',
            'newThisMonth',
            'salesUsers'
        ));
    }

    public function create(): View
    {
        $salesUsers = User::where('role', 'sales')->get();

        return view('customers.create', compact('salesUsers'));
    }

    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();

        if (auth()->user()->isSales()) {
            $data['assigned_user_id'] = auth()->id();
            $data['assignment_status'] = 'pending';
        }

        Customer::create($data);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->load(['assignedUser', 'leads', 'activities', 'followUps']);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        if (auth()->user()->isSales() && $customer->assigned_user_id !== auth()->id()) {
            return redirect()->route('customers.index')
                ->with('error', 'You do not have permission to edit this customer.');
        }

        $salesUsers = User::where('role', 'sales')->get();

        return view('customers.edit', compact('customer', 'salesUsers'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        if (auth()->user()->isSales() && $customer->assigned_user_id !== auth()->id()) {
            return redirect()->route('customers.show', $customer)
                ->with('error', 'You do not have permission to update this customer.');
        }

        $customer->update($request->validated());

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        if (! auth()->user()->isAdmin()) {
            return redirect()->route('customers.index')
                ->with('error', 'Only admins can delete customers.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    public function approve(Customer $customer)
    {
        if (! auth()->user()->isManager() && ! auth()->user()->isAdmin()) {
            return redirect()->back()
                ->with('error', 'Only managers and admins can approve customer assignments.');
        }

        $customer->update([
            'assignment_status' => 'approved',
            'assignment_reviewed_by' => auth()->id(),
            'assignment_reviewed_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Customer assignment approved.');
    }

    public function reject(Customer $customer)
    {
        if (! auth()->user()->isManager() && ! auth()->user()->isAdmin()) {
            return redirect()->back()
                ->with('error', 'Only managers and admins can reject customer assignments.');
        }

        $customer->update([
            'assignment_status' => 'rejected',
            'assignment_reviewed_by' => auth()->id(),
            'assignment_reviewed_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Customer assignment rejected.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FollowUp;
use App\Models\Lead;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $fromDate = $request->get('from');
        $toDate = $request->get('to');

        $query = Lead::query();
        $customerQuery = Customer::query();
        $followUpQuery = FollowUp::query();

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
            $customerQuery->whereDate('created_at', '>=', $fromDate);
            $followUpQuery->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
            $customerQuery->whereDate('created_at', '<=', $toDate);
            $followUpQuery->whereDate('created_at', '<=', $toDate);
        }

        $totalCustomers = $customerQuery->count();
        $pipelineLeads = $query->whereIn('status', ['new', 'contacted', 'qualified', 'proposal_sent', 'negotiation'])->count();
        $wonLeads = $query->where('status', 'won')->count();

        $totalFollowUps = $followUpQuery->count();
        $completedFollowUps = $followUpQuery->where('status', 'completed')->count();
        $completionRate = $totalFollowUps > 0 ? round(($completedFollowUps / $totalFollowUps) * 100) : 0;

        $leadStatusCounts = Lead::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $activeLeads = Lead::whereIn('status', ['new', 'contacted', 'qualified', 'proposal_sent', 'negotiation'])->count();
        $lostLeads = Lead::where('status', 'lost')->count();
        $wonLeadsValue = Lead::where('status', 'won')->sum('expected_value');

        return view('reports.index', compact(
            'totalCustomers',
            'pipelineLeads',
            'wonLeads',
            'completionRate',
            'leadStatusCounts',
            'activeLeads',
            'lostLeads',
            'wonLeadsValue',
            'fromDate',
            'toDate'
        ));
    }

    public function exportCsv(Request $request)
    {
        $fromDate = $request->get('from');
        $toDate = $request->get('to');

        $query = Lead::query();

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $leads = $query->get();

        $filename = 'leads_report_'.now()->format('Y-m-d').'.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Lead ID', 'Name', 'Email', 'Phone', 'Status', 'Priority', 'Expected Value', 'Created At']);

        foreach ($leads as $lead) {
            fputcsv($output, [
                $lead->lead_id,
                $lead->name,
                $lead->email,
                $lead->phone,
                $lead->status,
                $lead->priority,
                $lead->expected_value,
                $lead->created_at->format('Y-m-d'),
            ]);
        }

        fclose($output);
        exit;
    }

    public function exportPdf(Request $request)
    {
        $fromDate = $request->get('from');
        $toDate = $request->get('to');

        $totalCustomers = Customer::count();
        $pipelineLeads = Lead::whereIn('status', ['new', 'contacted', 'qualified', 'proposal_sent', 'negotiation'])->count();
        $wonLeads = Lead::where('status', 'won')->count();

        $leadStatusCounts = Lead::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $html = view('reports.pdf', compact('totalCustomers', 'pipelineLeads', 'wonLeads', 'leadStatusCounts', 'fromDate', 'toDate'))->render();

        $pdf = Pdf::loadHTML($html);
        $pdf->render();

        return $pdf->stream('reports_'.now()->format('Y-m-d').'.pdf');
    }
}

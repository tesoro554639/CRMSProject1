<?php

namespace App\Http\Controllers;

use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemConfigController extends Controller
{
    public function index(): View
    {
        $config = SystemConfig::getConfig();

        return view('settings.index', compact('config'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'currency_code' => 'required|string|max:10',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string',
            'default_lead_status' => 'required|in:new,contacted,qualified,proposal_sent,negotiation,won,lost',
            'default_lead_priority' => 'required|in:low,medium,high,critical',
            'reset_link_expiry' => 'required|integer|min:1|max:10080',
        ]);

        $config = SystemConfig::getConfig();
        $config->update($request->all());

        return redirect()->back()
            ->with('success', 'Configuration saved successfully.');
    }
}

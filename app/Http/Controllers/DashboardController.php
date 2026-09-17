<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Message;

class DashboardController extends Controller
{
    public function index()
    {
        $totalContacts = Contact::count();
        $totalCampaigns = Campaign::count();
        $totalSent = Message::where('direction', 'outbound')->whereIn('status', ['sent', 'delivered', 'read'])->count();
        $totalInbound = Message::where('direction', 'inbound')->count();

        $recentMessages = Message::with('contact')
            ->latest()
            ->take(6)
            ->get();

        $recentCampaigns = Campaign::latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalContacts',
            'totalCampaigns',
            'totalSent',
            'totalInbound',
            'recentMessages',
            'recentCampaigns'
        ));
    }
}

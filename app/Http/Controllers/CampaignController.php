<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsAppBroadcastJob;
use App\Models\BroadcastQueue;
use App\Models\Campaign;
use App\Models\Contact;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::latest()->paginate(10);
        $groups = Contact::select('group_tag')->distinct()->pluck('group_tag')->filter();

        return view('campaigns.index', compact('campaigns', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'template_name' => 'required|string|max:255',
            'template_language' => 'required|string|max:10',
            'target_group' => 'nullable|string',
        ]);

        $contactsQuery = Contact::query();
        if ($request->filled('target_group')) {
            $contactsQuery->where('group_tag', $request->target_group);
        }
        $contacts = $contactsQuery->get();

        if ($contacts->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada kontak yang ditemukan pada grup tersebut.');
        }

        $campaign = Campaign::create([
            'name' => $validated['name'],
            'template_name' => $validated['template_name'],
            'template_language' => $validated['template_language'],
            'status' => 'processing',
            'total_recipients' => $contacts->count(),
        ]);

        foreach ($contacts as $contact) {
            // Contoh parsing parameter otomatis: {{1}} nama kontak
            $params = [$contact->name];

            $queue = BroadcastQueue::create([
                'campaign_id' => $campaign->id,
                'contact_id' => $contact->id,
                'template_parameters' => $params,
                'status' => 'pending',
            ]);

            // Masukkan ke queue database Laravel
            SendWhatsAppBroadcastJob::dispatch($queue->id);
        }

        return redirect()->route('campaigns.index')->with('success', 'Kampanye blast berhasil dibuat dan masuk antrean!');
    }
}

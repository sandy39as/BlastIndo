<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Message;
use App\Services\WhatsAppCloudApiService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        // Ambil kontak yang memiliki riwayat pesan, diurutkan dari pesan terbaru
        $contacts = Contact::whereHas('messages')
            ->with(['messages' => function ($query) {
                $query->latest();
            }])
            ->get()
            ->sortByDesc(function ($contact) {
                return $contact->messages->first()?->created_at;
            });

        // Tentukan kontak aktif yang sedang dibuka chat-nya
        $activeContactId = $request->query('contact_id', $contacts->first()?->id);
        $activeContact = null;
        $messages = collect();

        if ($activeContactId) {
            $activeContact = Contact::find($activeContactId);
            if ($activeContact) {
                $messages = Message::where('contact_id', $activeContact->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        return view('chat.index', compact('contacts', 'activeContact', 'messages'));
    }

    public function sendMessage(Request $request, WhatsAppCloudApiService $waService)
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'message' => 'required|string',
        ]);

        $contact = Contact::findOrFail($validated['contact_id']);

        // Kirim pesan teks bebas ke WhatsApp pelanggan via Graph API Meta
        $result = $waService->sendTextMessage($contact->phone_number, $validated['message']);

        if ($result['success']) {
            $waMessageId = $result['data']['messages'][0]['id'] ?? null;

            Message::create([
                'contact_id' => $contact->id,
                'whatsapp_message_id' => $waMessageId,
                'direction' => 'outbound',
                'type' => 'text',
                'body' => $validated['message'],
                'status' => 'sent',
            ]);

            return redirect()->route('chat.index', ['contact_id' => $contact->id]);
        }

        return redirect()->back()->with('error', 'Gagal mengirim pesan: ' . $result['error']);
    }
}

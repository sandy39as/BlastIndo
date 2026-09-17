<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Message;
use App\Models\MessageStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Verifikasi webhook saat didaftarkan di Meta App Dashboard (GET request)
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $secretToken = config('whatsapp.webhook_verify_token');

        if ($mode === 'subscribe' && $token === $secretToken) {
            return response($challenge, 200);
        }

        return response('Invalid verification token', 403);
    }

    /**
     * Menerima payload event dari Meta (POST request)
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        // Cek apakah event berasal dari akun WhatsApp Cloud API
        if (!isset($payload['object']) || $payload['object'] !== 'whatsapp_business_account') {
            return response('EVENT_RECEIVED', 200);
        }

        try {
            foreach ($payload['entry'] as $entry) {
                foreach ($entry['changes'] as $change) {
                    $value = $change['value'] ?? [];

                    // 1. Tangani Pesan Masuk (Inbound Message / Live Chat)
                    if (isset($value['messages'])) {
                        foreach ($value['messages'] as $incomingMessage) {
                            $this->processIncomingMessage($incomingMessage, $value['contacts'] ?? []);
                        }
                    }

                    // 2. Tangani Update Status Pesan (sent, delivered, read, failed)
                    if (isset($value['statuses'])) {
                        foreach ($value['statuses'] as $statusUpdate) {
                            $this->processStatusUpdate($statusUpdate);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Webhook Processing Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        // Meta mewajibkan HTTP 200 segera agar tidak mengirim ulang payload
        return response('EVENT_RECEIVED', 200);
    }

    /**
     * Simpan pesan masuk ke database
     */
    protected function processIncomingMessage(array $waMsg, array $waContacts): void
    {
        $fromPhone = $waMsg['from']; // Nomor pelanggan format 628xxx
        $messageId = $waMsg['id'];

        // Cek duplikasi pesan (idempotency)
        if (Message::where('whatsapp_message_id', $messageId)->exists()) {
            return;
        }

        // Ambil nama profil WhatsApp jika dikirimkan oleh Meta
        $contactName = $waContacts[0]['profile']['name'] ?? 'Pelanggan';

        // Temukan atau buat kontak secara otomatis
        $contact = Contact::firstOrCreate(
            ['phone_number' => $fromPhone],
            [
                'name' => $contactName,
                'group_tag' => 'Inbox Webhook',
            ]
        );

        $type = $waMsg['type'] ?? 'text';
        $body = null;
        $media = null;

        if ($type === 'text') {
            $body = $waMsg['text']['body'] ?? '';
        } elseif (in_array($type, ['image', 'document', 'audio', 'video'])) {
            $body = $waMsg[$type]['caption'] ?? "[$type]";
            $media = [
                'id' => $waMsg[$type]['id'] ?? null,
                'mime_type' => $waMsg[$type]['mime_type'] ?? null,
            ];
        } elseif ($type === 'button') {
            $body = $waMsg['button']['text'] ?? '';
        } elseif ($type === 'interactive') {
            $body = $waMsg['interactive']['button_reply']['title'] 
                ?? $waMsg['interactive']['list_reply']['title'] 
                ?? '[interactive response]';
        }

        Message::create([
            'contact_id' => $contact->id,
            'whatsapp_message_id' => $messageId,
            'direction' => 'inbound',
            'type' => $type,
            'body' => $body,
            'media_payload' => $media,
            'status' => 'delivered', // Pesan dari user otomatis valid masuk
        ]);
    }

    /**
     * Catat log perubahan status pengiriman pesan
     */
    protected function processStatusUpdate(array $status): void
    {
        $waMessageId = $status['id'];
        $newStatus = $status['status']; // sent, delivered, read, failed
        $timestamp = isset($status['timestamp']) 
            ? Carbon::createFromTimestamp($status['timestamp']) 
            : now();

        $message = Message::where('whatsapp_message_id', $waMessageId)->first();

        if ($message) {
            $message->update([
                'status' => $newStatus,
                'error_details' => isset($status['errors']) ? json_encode($status['errors']) : null,
            ]);

            MessageStatusLog::create([
                'message_id' => $message->id,
                'status' => $newStatus,
                'occurred_at' => $timestamp,
                'raw_payload' => $status,
            ]);
        }
    }
}

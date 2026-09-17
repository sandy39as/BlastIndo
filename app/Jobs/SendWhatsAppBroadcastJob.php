<?php

namespace App\Jobs;

use App\Models\BroadcastQueue;
use App\Models\Message;
use App\Services\WhatsAppCloudApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    protected int $broadcastQueueId;

    public function __construct(int $broadcastQueueId)
    {
        $this->broadcastQueueId = $broadcastQueueId;
    }

    public function handle(WhatsAppCloudApiService $waService): void
    {
        $queueItem = BroadcastQueue::with(['contact', 'campaign'])->find($this->broadcastQueueId);

        if (!$queueItem || $queueItem->status === 'sent') {
            return;
        }

        $campaign = $queueItem->campaign;
        $contact = $queueItem->contact;

        // Siapkan parameter template body jika ada variabel {{1}}, {{2}}
        $components = [];
        if (!empty($queueItem->template_parameters)) {
            $parameters = [];
            foreach ($queueItem->template_parameters as $param) {
                $parameters[] = [
                    'type' => 'text',
                    'text' => $param,
                ];
            }

            $components[] = [
                'type' => 'body',
                'parameters' => $parameters,
            ];
        }

        // Kirim request template via Service Meta
        $result = $waService->sendTemplateMessage(
            $contact->phone_number,
            $campaign->template_name,
            $campaign->template_language,
            $components
        );

        if ($result['success']) {
            $waMessageId = $result['data']['messages'][0]['id'] ?? null;

            $queueItem->update([
                'status' => 'sent',
                'dispatched_at' => now(),
            ]);

            // Catat ke tabel messages
            Message::create([
                'contact_id' => $contact->id,
                'campaign_id' => $campaign->id,
                'whatsapp_message_id' => $waMessageId,
                'direction' => 'outbound',
                'type' => 'template',
                'body' => 'Template: ' . $campaign->template_name,
                'status' => 'sent',
            ]);

            $campaign->increment('total_sent');
        } else {
            $queueItem->update([
                'status' => 'failed',
                'error_message' => $result['error'],
            ]);

            $campaign->increment('total_failed');
        }
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppCloudApiService
{
    protected string $apiUrl;
    protected string $phoneNumberId;
    protected string $accessToken;

    public function __construct()
    {
        $this->apiUrl = config('whatsapp.api_url');
        $this->phoneNumberId = config('whatsapp.phone_number_id');
        $this->accessToken = config('whatsapp.access_token');
    }

    /**
     * Kirim template pesan resmi (Untuk Blast/Broadcast)
     */
    public function sendTemplateMessage(string $toPhoneNumber, string $templateName, string $languageCode = 'id', array $components = []): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $toPhoneNumber,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode,
                ],
            ],
        ];

        if (!empty($components)) {
            $payload['template']['components'] = $components;
        }

        return $this->sendRequest($payload);
    }

    /**
     * Kirim pesan teks manual 2 arah (Untuk fitur Live Chat / Reply Inbox)
     */
    public function sendTextMessage(string $toPhoneNumber, string $textMessage): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $toPhoneNumber,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $textMessage,
            ],
        ];

        return $this->sendRequest($payload);
    }

    /**
     * Eksekusi HTTP Request ke Meta
     */
    protected function sendRequest(array $payload): array
    {
        $endpoint = "{$this->apiUrl}/{$this->phoneNumberId}/messages";

        try {
            $response = Http::withToken($this->accessToken)
                ->timeout(20)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Meta WA API Error', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return [
                'success' => false,
                'error' => $response->json()['error']['message'] ?? 'Terjadi kesalahan pada Meta API.',
            ];
        } catch (\Exception $e) {
            Log::error('Meta WA Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}

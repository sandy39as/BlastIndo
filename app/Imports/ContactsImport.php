<?php

namespace App\Imports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContactsImport implements ToModel, WithHeadingRow
{
    protected ?string $defaultGroup;

    public function __construct(?string $defaultGroup = null)
    {
        $this->defaultGroup = $defaultGroup;
    }

    public function model(array $row)
    {
        $rawPhone = preg_replace('/[^0-9]/', '', (string)($row['telepon'] ?? $row['phone'] ?? $row['nomor_hp'] ?? ''));

        // Konversi format lokal 08xxx ke 628xxx
        if (str_starts_with($rawPhone, '0')) {
            $rawPhone = '62' . substr($rawPhone, 1);
        }

        if (empty($rawPhone)) {
            return null;
        }

        return Contact::updateOrCreate(
            ['phone_number' => $rawPhone],
            [
                'name' => $row['nama'] ?? $row['name'] ?? 'Pelanggan',
                'email' => $row['email'] ?? null,
                'group_tag' => $row['group'] ?? $row['grup'] ?? $this->defaultGroup ?? 'Umum',
            ]
        );
    }
}

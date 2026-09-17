<?php

namespace App\Http\Controllers;

use App\Imports\ContactsImport;
use App\Models\Contact;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('group')) {
            $query->where('group_tag', $request->group);
        }

        $contacts = $query->latest()->paginate(15);
        $groups = Contact::select('group_tag')->distinct()->pluck('group_tag')->filter();

        return view('contacts.index', compact('contacts', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'group_tag' => 'nullable|string|max:100',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $validated['phone_number']);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        $validated['phone_number'] = $phone;

        Contact::updateOrCreate(
            ['phone_number' => $phone],
            $validated
        );

        return redirect()->back()->with('success', 'Kontak berhasil disimpan.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:5120',
            'group_tag' => 'nullable|string|max:100',
        ]);

        Excel::import(new ContactsImport($request->group_tag), $request->file('file'));

        return redirect()->back()->with('success', 'Kontak massal berhasil diimport.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->back()->with('success', 'Kontak berhasil dihapus.');
    }
}

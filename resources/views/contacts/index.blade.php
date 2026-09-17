@extends('layouts.app')

@section('page_title', 'Daftar Kontak - BlastIndo')

@section('content')
<div class="space-y-6">

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg flex items-center justify-between">
        <span><i class="fa-solid fa-circle-check mr-2 text-emerald-600"></i> {{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
    </div>
    @endif

    <!-- Toolbar Atas: Search & Action Modals -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('contacts.index') }}" class="flex gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / nomor..." class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none w-64">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm transition">Cari</button>
        </form>

        <div class="flex gap-2 w-full md:w-auto">
            <button onclick="document.getElementById('modal-import').classList.remove('hidden')" class="bg-emerald-100 text-emerald-800 hover:bg-emerald-200 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <i class="fa-solid fa-file-excel"></i> Import File
            </button>
            <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Kontak
            </button>
        </div>
    </div>

    <!-- Tabel Kontak -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                <tr>
                    <th class="py-3 px-6">Nama</th>
                    <th class="py-3 px-6">Nomor Telepon</th>
                    <th class="py-3 px-6">Grup</th>
                    <th class="py-3 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($contacts as $contact)
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3.5 px-6 font-medium text-slate-800">{{ $contact->name }}</td>
                    <td class="py-3.5 px-6 text-slate-600 font-mono">{{ $contact->phone_number }}</td>
                    <td class="py-3.5 px-6">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                            {{ $contact->group_tag ?? 'Umum' }}
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-right">
                        <form method="POST" action="{{ route('contacts.destroy', $contact) }}" onsubmit="return confirm('Hapus kontak ini?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:text-rose-700 transition">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-slate-400">Belum ada data kontak.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">
            {{ $contacts->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah Kontak -->
<div id="modal-tambah" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Tambah Kontak Baru</h3>
        <form method="POST" action="{{ route('contacts.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nama</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor WhatsApp (Contoh: 08123456789 atau 628123456789)</label>
                <input type="text" name="phone_number" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Grup / Tag</label>
                <input type="text" name="group_tag" placeholder="Pelanggan VIP, Calon Siswa, dll" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg text-sm text-slate-600">Batal</button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm">Simpan Kontak</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import Excel -->
<div id="modal-import" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-2">Import File Excel/CSV</h3>
        <p class="text-xs text-slate-500 mb-4">Pastikan header kolom berisi: <strong>nama</strong> dan <strong>telepon</strong>.</p>
        <form method="POST" action="{{ route('contacts.import') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih File (.xlsx, .csv)</label>
                <input type="file" name="file" required accept=".xlsx,.csv,.xls" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tag Grup untuk Semua Kontak Ini (Opsional)</label>
                <input type="text" name="group_tag" placeholder="Import September, dll" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-import').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg text-sm text-slate-600">Batal</button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm">Upload & Proses</button>
            </div>
        </form>
    </div>
</div>
@endsection

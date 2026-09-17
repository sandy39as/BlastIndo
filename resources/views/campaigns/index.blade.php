@extends('layouts.app')

@section('page_title', 'Broadcast Blast - BlastIndo')

@section('content')
<div class="space-y-6">

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg flex items-center justify-between">
        <span><i class="fa-solid fa-circle-check mr-2 text-emerald-600"></i> {{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="text-emerald-500">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg flex items-center justify-between">
        <span><i class="fa-solid fa-circle-xmark mr-2 text-rose-600"></i> {{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" class="text-rose-500">&times;</button>
    </div>
    @endif

    <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="font-bold text-slate-800">Kampanye Pengiriman Massal</h2>
            <p class="text-xs text-slate-500">Kirim template resmi WhatsApp Meta ke seluruh kontak tertarget.</p>
        </div>
        <button onclick="document.getElementById('modal-campaign').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <i class="fa-solid fa-paper-plane"></i> Buat Blast Baru
        </button>
    </div>

    <!-- Tabel Riwayat Kampanye -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                <tr>
                    <th class="py-3 px-6">Nama Kampanye</th>
                    <th class="py-3 px-6">Template</th>
                    <th class="py-3 px-6">Status</th>
                    <th class="py-3 px-6">Progres Terkirim</th>
                    <th class="py-3 px-6">Waktu Dibuat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($campaigns as $camp)
                <tr class="hover:bg-slate-50">
                    <td class="py-4 px-6 font-semibold text-slate-800">{{ $camp->name }}</td>
                    <td class="py-4 px-6 font-mono text-xs text-slate-600">{{ $camp->template_name }} ({{ $camp->template_language }})</td>
                    <td class="py-4 px-6">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold 
                            {{ $camp->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ ucfirst($camp->status) }}
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="text-xs font-semibold text-slate-700 mb-1">
                            {{ $camp->total_sent }} / {{ $camp->total_recipients }} Sukses
                            @if($camp->total_failed > 0)
                                <span class="text-rose-500">({{ $camp->total_failed }} Gagal)</span>
                            @endif
                        </div>
                        <div class="w-48 bg-slate-200 rounded-full h-2">
                            @php
                                $percent = $camp->total_recipients > 0 ? ($camp->total_sent / $camp->total_recipients) * 100 : 0;
                            @endphp
                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-slate-500 text-xs">{{ $camp->created_at->format('d M Y, H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-400">Belum ada aktivitas kampanye blast.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">
            {{ $campaigns->links() }}
        </div>
    </div>
</div>

<!-- Modal Blast Baru -->
<div id="modal-campaign" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Luncurkan Broadcast Baru</h3>
        <form method="POST" action="{{ route('campaigns.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Kampanye</label>
                <input type="text" name="name" required placeholder="Promo September, Info Registrasi..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Template Meta</label>
                    <input type="text" name="template_name" required placeholder="promo_diskon" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Bahasa Template</label>
                    <input type="text" name="template_language" value="id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Target Kontak</label>
                <select name="target_group" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Kontak</option>
                    @foreach($groups as $group)
                        <option value="{{ $group }}">Grup: {{ $group }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="document.getElementById('modal-campaign').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg text-sm text-slate-600">Batal</button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Mulai Kirim Antrean</button>
            </div>
        </form>
    </div>
</div>
@endsection

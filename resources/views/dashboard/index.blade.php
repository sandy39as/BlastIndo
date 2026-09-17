@extends('layouts.app')

@section('page_title', 'Ringkasan Sistem - BlastIndo')

@section('content')
<div class="space-y-8">

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kontak</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($totalContacts) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pesan Keluar</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($totalSent) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pesan Masuk (Inbox)</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($totalInbound) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-inbox"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kampanye</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($totalCampaigns) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Aktivitas Pesan Terbaru -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-800 mb-4">Aktivitas Pesan Terkini</h3>
            <div class="divide-y divide-slate-100">
                @forelse($recentMessages as $msg)
                <div class="py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $msg->direction === 'inbound' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $msg->direction === 'inbound' ? 'IN' : 'OUT' }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $msg->contact->name }}</p>
                            <p class="text-xs text-slate-500 truncate max-w-xs">{{ $msg->body }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-medium px-2 py-0.5 rounded {{ $msg->status === 'read' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ ucfirst($msg->status) }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-4">Belum ada aktivitas chat.</p>
                @endforelse
            </div>
        </div>

        <!-- Kampanye Terakhir -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-800 mb-4">Kampanye Terakhir</h3>
            <div class="divide-y divide-slate-100">
                @forelse($recentCampaigns as $camp)
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ $camp->name }}</p>
                        <p class="text-xs text-slate-400 font-mono">Tpl: {{ $camp->template_name }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-semibold text-emerald-600">{{ $camp->total_sent }} / {{ $camp->total_recipients }}</span>
                        <p class="text-[10px] text-slate-400">{{ $camp->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-4">Belum ada kampanye dibuat.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

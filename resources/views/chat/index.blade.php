@extends('layouts.app')

@section('page_title', 'Live Chat Inbox - BlastIndo')

@section('content')
<div class="h-[calc(100vh-8.5rem)] flex bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">

    <!-- Kolom Kiri: Daftar Kontak Percakapan -->
    <div class="w-80 border-r border-slate-200 flex flex-col bg-slate-50/50">
        <div class="p-4 border-b border-slate-200 bg-white">
            <h2 class="font-bold text-slate-800">Percakapan</h2>
            <p class="text-xs text-slate-500">Pelanggan yang berinteraksi</p>
        </div>

        <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
            @forelse($contacts as $contactItem)
                @php
                    $lastMsg = $contactItem->messages->first();
                    $isActive = $activeContact && $activeContact->id === $contactItem->id;
                @endphp
                <a href="{{ route('chat.index', ['contact_id' => $contactItem->id]) }}" 
                   class="flex items-center gap-3 p-4 transition block {{ $isActive ? 'bg-emerald-50 border-l-4 border-emerald-600' : 'hover:bg-slate-100/70' }}">
                    <div class="w-10 h-10 rounded-full bg-emerald-700 text-white font-bold flex items-center justify-center flex-shrink-0">
                        {{ strtoupper(substr($contactItem->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline mb-0.5">
                            <h3 class="text-sm font-semibold text-slate-800 truncate">{{ $contactItem->name }}</h3>
                            <span class="text-[10px] text-slate-400">{{ $lastMsg?->created_at?->format('H:i') }}</span>
                        </div>
                        <p class="text-xs text-slate-500 truncate">
                            @if($lastMsg?->direction === 'outbound')
                                <span class="text-emerald-600 font-medium">Anda: </span>
                            @endif
                            {{ $lastMsg?->body ?? 'Mulai percakapan' }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="p-6 text-center text-xs text-slate-400">
                    Belum ada riwayat pesan masuk.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Kolom Kanan: Area Chat Aktif -->
    @if($activeContact)
    <div class="flex-1 flex flex-col bg-slate-100/40">
        <!-- Header Chat -->
        <div class="h-16 px-6 bg-white border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm">
                    {{ strtoupper(substr($activeContact->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 leading-tight">{{ $activeContact->name }}</h3>
                    <span class="text-xs text-slate-500 font-mono">{{ $activeContact->phone_number }}</span>
                </div>
            </div>
            <span class="text-xs bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full font-medium">
                {{ $activeContact->group_tag ?? 'Pelanggan' }}
            </span>
        </div>

        <!-- Bubble Percakapan -->
        <div id="chat-box" class="flex-1 overflow-y-auto p-6 space-y-4">
            @foreach($messages as $msg)
                @if($msg->direction === 'outbound')
                    <!-- Pesan Keluar (Admin) -->
                    <div class="flex flex-col items-end">
                        <div class="max-w-md bg-emerald-600 text-white px-4 py-2.5 rounded-2xl rounded-tr-none text-sm shadow-sm">
                            <p class="whitespace-pre-wrap">{{ $msg->body }}</p>
                        </div>
                        <div class="flex items-center gap-1 mt-1 text-[10px] text-slate-400">
                            <span>{{ $msg->created_at->format('H:i') }}</span>
                            @if($msg->status === 'read')
                                <i class="fa-solid fa-check-double text-emerald-500"></i>
                            @elseif($msg->status === 'delivered')
                                <i class="fa-solid fa-check-double"></i>
                            @else
                                <i class="fa-solid fa-check"></i>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Pesan Masuk (Pelanggan) -->
                    <div class="flex flex-col items-start">
                        <div class="max-w-md bg-white border border-slate-200 text-slate-800 px-4 py-2.5 rounded-2xl rounded-tl-none text-sm shadow-sm">
                            <p class="whitespace-pre-wrap">{{ $msg->body }}</p>
                        </div>
                        <span class="mt-1 text-[10px] text-slate-400">{{ $msg->created_at->format('H:i') }}</span>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Input Bar Balas Chat -->
        <div class="p-4 bg-white border-t border-slate-200">
            <form action="{{ route('chat.send') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="hidden" name="contact_id" value="{{ $activeContact->id }}">
                <input type="text" name="message" required autocomplete="off" placeholder="Ketik balasan pesan di sini..." 
                       class="flex-1 px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2">
                    <span>Kirim</span>
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="flex-1 flex flex-col items-center justify-center text-slate-400 bg-slate-50">
        <i class="fa-regular fa-comment-dots text-5xl mb-3 text-slate-300"></i>
        <p class="text-sm">Pilih salah satu kontak di sisi kiri untuk membuka chat.</p>
    </div>
    @endif
</div>

<script>
    // Otomatis scroll ke pesan terbawah saat halaman dibuka
    const chatBox = document.getElementById('chat-box');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
</script>
@endsection

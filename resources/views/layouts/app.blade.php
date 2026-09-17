<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - BlastIndo</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 flex h-screen overflow-hidden">

    <!-- Sidebar Emerald -->
    <aside class="w-64 bg-primary-800 text-white flex flex-col flex-shrink-0 shadow-lg">
        <div class="h-16 flex items-center px-6 bg-primary-900 font-bold text-xl tracking-wider gap-2">
            <i class="fa-brands fa-whatsapp text-2xl text-primary-400"></i>
            <span>Blast<span class="text-primary-400">Indo</span></span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1">
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary-700 text-white font-medium">
                <i class="fa-solid fa-chart-pie w-5"></i> Dashboard
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary-700/60 transition text-emerald-100 font-medium">
                <i class="fa-solid fa-address-book w-5"></i> Kontak
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary-700/60 transition text-emerald-100 font-medium">
                <i class="fa-solid fa-paper-plane w-5"></i> Broadcast Blast
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary-700/60 transition text-emerald-100 font-medium">
                <i class="fa-solid fa-comments w-5"></i> Live Chat Inbox
            </a>
        </nav>
        <div class="p-4 border-t border-primary-700 text-xs text-primary-300 text-center">
            BlastIndo Engine v1.0
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-y-auto">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h1 class="text-lg font-semibold text-slate-700">@yield('page_title', 'Dashboard')</h1>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                    <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    API Connected
                </span>
            </div>
        </header>

        <main class="p-8">
            @yield('content')
        </main>
    </div>

</body>
</html>

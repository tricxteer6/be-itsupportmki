<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="min-h-screen lg:flex">
        <aside class="w-full border-b border-slate-200 bg-white lg:min-h-screen lg:w-72 lg:border-b-0 lg:border-r">
            <div class="px-4 py-5">
                <div>
                    <p class="text-xs uppercase tracking-wide text-orange-600">Master Kuliner Indonesia</p>
                    <h1 class="text-lg font-semibold">Admin Dashboard</h1>
                </div>
                @auth
                    <div class="mt-5 rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-600">
                        <p class="font-semibold text-slate-800">{{ auth()->user()->nama_karyawan }}</p>
                        <p class="text-xs text-slate-500">Administrator</p>
                    </div>
                    <div class="mt-3 rounded-xl border border-orange-100 bg-orange-50 px-3 py-2 text-xs text-orange-700">
                        <i class="ri-pulse-line mr-1"></i> Realtime notifikasi aktif.
                    </div>
                @endauth

                <nav class="mt-5 grid gap-2">
                    <a href="{{ route('admin.tickets.index') }}"
                       class="rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.tickets.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        <i class="ri-ticket-2-line mr-2"></i> Ticket Management
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                       class="rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        <i class="ri-user-settings-line mr-2"></i> User Management
                    </a>
                </nav>

                @auth
                    <form method="POST" action="{{ route('admin.logout') }}" class="mt-5">
                        @csrf
                        <button type="submit" class="w-full rounded-xl bg-slate-900 px-3 py-2.5 text-sm font-medium text-white hover:bg-slate-800">
                            <i class="ri-logout-box-r-line mr-1"></i> Logout
                        </button>
                    </form>
                @endauth
            </div>
        </aside>

        <main class="w-full p-4 sm:p-6">
            <div class="mb-4 flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Portal Admin</p>
                    <p class="text-sm font-semibold text-slate-800">@yield('title', 'Admin Dashboard')</p>
                </div>
                @auth
                    <div class="relative" id="adminNotifRoot">
                        <button type="button" id="notifToggle" class="relative rounded-xl border border-slate-200 bg-white p-2 text-slate-700 hover:bg-slate-50">
                            <i class="ri-notification-3-line text-xl"></i>
                            <span id="notifBadge" class="absolute -right-1 -top-1 hidden min-h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white"></span>
                        </button>
                        <div id="notifPanel" class="absolute right-0 top-full z-40 mt-2 hidden w-80 rounded-2xl border border-slate-200 bg-white py-2 shadow-xl">
                            <div class="flex items-center justify-between border-b border-slate-100 px-3 pb-2">
                                <p class="text-sm font-semibold text-slate-800">Notifikasi</p>
                                <button type="button" id="notifMarkAll" class="text-xs font-medium text-orange-600 hover:underline">Tandai dibaca</button>
                            </div>
                            <div id="notifList" class="max-h-80 overflow-y-auto p-2"></div>
                        </div>
                    </div>
                @endauth
            </div>

            @if (session('success'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @auth
        <script>
            (() => {
                const root = document.getElementById('adminNotifRoot');
                if (!root) return;

                const toggle = document.getElementById('notifToggle');
                const panel = document.getElementById('notifPanel');
                const badge = document.getElementById('notifBadge');
                const list = document.getElementById('notifList');
                const markAll = document.getElementById('notifMarkAll');
                const feedUrl = "{{ route('admin.notifications.feed') }}";
                const markAllUrl = "{{ route('admin.notifications.mark-all-read') }}";
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                let markAllInFlight = false;

                const render = (payload) => {
                    const unread = payload.unread_count || 0;
                    if (unread > 0) {
                        badge.classList.remove('hidden');
                        badge.classList.add('inline-flex');
                        badge.textContent = unread > 9 ? '9+' : unread;
                    } else {
                        badge.classList.add('hidden');
                        badge.classList.remove('inline-flex');
                    }

                    if (!payload.items || payload.items.length === 0) {
                        list.innerHTML = '<p class="px-2 py-4 text-center text-sm text-slate-500">Belum ada notifikasi</p>';
                        return;
                    }

                    list.innerHTML = payload.items.map((n) => `
                        <div class="rounded-lg px-2 py-2 text-sm hover:bg-slate-50">
                            <p class="font-medium text-slate-800">${n.title ?? 'Pembaruan tiket'}</p>
                            ${n.body ? `<p class="mt-0.5 text-xs text-slate-600">${n.body}</p>` : ''}
                            <p class="mt-1 text-[11px] text-slate-400">${n.created_at_human ?? ''}</p>
                        </div>
                    `).join('');
                };

                const loadFeed = async () => {
                    try {
                        const response = await fetch(feedUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
                        if (!response.ok) return;
                        const data = await response.json();
                        render(data);
                    } catch (_) {
                    }
                };

                const markAllRead = async () => {
                    if (markAllInFlight) return;
                    markAllInFlight = true;
                    try {
                        await fetch(markAllUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({})
                        });
                        await loadFeed();
                    } catch (_) {
                    } finally {
                        markAllInFlight = false;
                    }
                };

                toggle.addEventListener('click', async () => {
                    panel.classList.toggle('hidden');
                    if (!panel.classList.contains('hidden') && !badge.classList.contains('hidden')) {
                        await markAllRead();
                    }
                });
                document.addEventListener('click', (e) => {
                    if (!root.contains(e.target)) panel.classList.add('hidden');
                });

                markAll.addEventListener('click', markAllRead);

                loadFeed();
                setInterval(loadFeed, 10000);

            })();
        </script>
    @endauth
    @stack('scripts')
</body>
</html>

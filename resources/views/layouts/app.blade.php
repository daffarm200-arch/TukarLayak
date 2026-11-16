<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'TukarLayak') - Platform Tukar & Berbagi Barang Layak Pakai</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased">

    <!-- Header / Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 bg-brand-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-brand-600/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-slate-900">
                            Tukar<span class="text-brand-600">Layak</span>
                        </span>
                    </a>

                    <!-- Main Navigation Links -->
                    <nav class="hidden lg:flex items-center space-x-1">
                        @auth
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('admin.dashboard') ? 'text-purple-700 bg-purple-50' : 'text-slate-600 hover:text-purple-600 hover:bg-slate-50' }} transition">
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('admin.users.*') ? 'text-purple-700 bg-purple-50' : 'text-slate-600 hover:text-purple-600 hover:bg-slate-50' }} transition">
                                    Pengguna
                                </a>
                                <a href="{{ route('admin.items.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('admin.items.*') ? 'text-purple-700 bg-purple-50' : 'text-slate-600 hover:text-purple-600 hover:bg-slate-50' }} transition">
                                    Kelola Barang
                                </a>
                                <a href="{{ route('admin.categories.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('admin.categories.*') ? 'text-purple-700 bg-purple-50' : 'text-slate-600 hover:text-purple-600 hover:bg-slate-50' }} transition">
                                    Kategori
                                </a>
                                <a href="{{ route('admin.reports.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('admin.reports.*') ? 'text-purple-700 bg-purple-50' : 'text-slate-600 hover:text-purple-600 hover:bg-slate-50' }} transition">
                                    Laporan
                                </a>
                                <span class="text-slate-300">|</span>
                            @endif
                        @endauth

                        <a href="{{ route('items.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('items.index') ? 'text-brand-700 bg-brand-50' : 'text-slate-600 hover:text-brand-600 hover:bg-slate-50' }} transition">
                            Katalog Publik
                        </a>

                        @auth
                            @if(!Auth::user()->isAdmin())
                                <a href="{{ route('items.my') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('items.my*') ? 'text-brand-700 bg-brand-50' : 'text-slate-600 hover:text-brand-600 hover:bg-slate-50' }} transition">
                                    Barang Saya
                                </a>
                                <a href="{{ route('requests.incoming') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('requests.incoming') ? 'text-brand-700 bg-brand-50' : 'text-slate-600 hover:text-brand-600 hover:bg-slate-50' }} transition">
                                    Permintaan Masuk
                                </a>
                                <a href="{{ route('requests.my') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('requests.my') ? 'text-brand-700 bg-brand-50' : 'text-slate-600 hover:text-brand-600 hover:bg-slate-50' }} transition">
                                    Permintaan Saya
                                </a>
                                <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request()->routeIs('dashboard') ? 'text-brand-700 bg-brand-50' : 'text-slate-600 hover:text-brand-600 hover:bg-slate-50' }} transition">
                                    Dashboard
                                </a>
                            @endif
                        @endauth
                    </nav>
                </div>

                <!-- Action & User Profile -->
                <div class="flex items-center space-x-3">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                🛡️ Admin
                            </span>
                        @else
                            <a href="{{ route('items.create') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-sm transition">
                                + Pasang Barang
                            </a>
                        @endif

                        <div class="hidden sm:flex items-center text-xs font-semibold text-slate-700 border-l border-slate-200 pl-3 ml-2">
                            {{ Auth::user()->name }}
                        </div>

                        <!-- Form Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Keluar" class="inline-flex items-center p-2 text-red-600 hover:bg-red-50 rounded-xl transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition px-3 py-2">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-sm transition">
                            Daftar Akun
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Notifikasi Sukses -->
        @if (session('success'))
            <div class="mb-6 flex items-center p-4 rounded-xl bg-brand-50 border border-brand-200 text-brand-800 shadow-sm" role="alert">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 text-brand-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        <!-- Notifikasi Error -->
        @if (session('error'))
            <div class="mb-6 flex items-center p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 shadow-sm" role="alert">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} <strong>TukarLayak</strong>. Platform Pertukaran & Berbagi Barang Layak Pakai.</p>
    </footer>

    <!-- Global Confirmation Modal -->
    <div id="app-confirm-modal"
         class="fixed inset-0 z-[100] hidden items-center justify-center p-4"
         aria-hidden="true">

        <!-- Overlay -->
        <button type="button"
                data-modal-close
                class="absolute inset-0 bg-slate-950/60 backdrop-blur-[3px]"
                aria-label="Tutup modal">
        </button>

        <!-- Modal -->
        <div class="relative w-full max-w-md rounded-3xl bg-[#FAFCFA] p-6 shadow-2xl"
             role="dialog"
             aria-modal="true"
             aria-labelledby="app-confirm-title"
             aria-describedby="app-confirm-message">

            <div class="flex flex-col items-center text-center">

                <!-- Icon -->
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#FCE9E8] text-[#B94B45]">
                    <svg class="h-8 w-8"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.8"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/>
                    </svg>
                </div>

                <h2 id="app-confirm-title"
                    class="mt-4 text-xl font-bold text-[#253B32]">
                    Hapus Barang?
                </h2>

                <p id="app-confirm-message"
                   class="mt-2 text-sm leading-relaxed text-[#718078]">
                    Apakah kamu yakin ingin menghapus data ini?
                    Tindakan ini tidak dapat dibatalkan.
                </p>

                <!-- Item Information -->
                <div id="app-confirm-item-wrapper"
                     class="mt-5 hidden w-full rounded-xl border border-[#E1EAE3] bg-[#F0F4F1] p-4 text-left">

                    <p id="app-confirm-item-label"
                       class="text-[10px] font-bold uppercase tracking-wider text-[#87948C]">
                        Barang yang akan dihapus
                    </p>

                    <p id="app-confirm-item"
                       class="mt-1 font-semibold text-[#253B32]">
                    </p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-7 grid grid-cols-2 gap-3">

                <button type="button"
                        data-modal-close
                        class="rounded-xl bg-[#E8EEE9] px-4 py-3 text-sm font-semibold text-[#43564B] transition hover:bg-[#DCE6DE] focus:outline-none focus:ring-2 focus:ring-[#A3B9A8]">
                    Batal
                </button>

                <button type="button"
                        id="app-confirm-submit"
                        class="rounded-xl bg-[#B94B45] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#A33D38] focus:outline-none focus:ring-2 focus:ring-[#D98D88] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                    Ya, Hapus
                </button>

            </div>
        </div>
    </div>

    <!-- Confirmation Modal Script -->
    <script>
        (() => {
            const modal = document.getElementById('app-confirm-modal');

            if (!modal) return;

            const title = document.getElementById('app-confirm-title');
            const message = document.getElementById('app-confirm-message');
            const itemWrapper = document.getElementById('app-confirm-item-wrapper');
            const itemLabel = document.getElementById('app-confirm-item-label');
            const itemName = document.getElementById('app-confirm-item');
            const confirmButton = document.getElementById('app-confirm-submit');

            const closeButtons = modal.querySelectorAll('[data-modal-close]');

            let pendingForm = null;
            let previousFocus = null;
            let isSubmitting = false;

            function openModal(form) {
                pendingForm = form;
                previousFocus = document.activeElement;
                isSubmitting = false;

                title.textContent = form.dataset.confirmTitle || 'Konfirmasi';
                message.textContent = form.dataset.confirmMessage || 'Apakah kamu yakin ingin melanjutkan?';

                const item = form.dataset.confirmItem || '';
                const kind = form.dataset.confirmKind || 'Barang';

                itemName.textContent = item;
                itemLabel.textContent = `${kind} yang akan dihapus`;
                itemWrapper.classList.toggle('hidden', !item);

                confirmButton.textContent = form.dataset.confirmButton || 'Ya, Hapus';

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');

                document.body.classList.add('overflow-hidden');

                closeButtons[0]?.focus();
            }

            function closeModal() {
                if (isSubmitting) return;

                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');

                document.body.classList.remove('overflow-hidden');

                pendingForm = null;

                if (previousFocus) {
                    previousFocus.focus();
                    previousFocus = null;
                }
            }

            document.addEventListener('submit', function (event) {
                const form = event.target;

                if (!(form instanceof HTMLFormElement)) return;
                if (!form.matches('form[data-app-confirm]')) return;

                if (form.dataset.confirmApproved === 'true') {
                    delete form.dataset.confirmApproved;
                    return;
                }

                event.preventDefault();
                openModal(form);
            });

            confirmButton.addEventListener('click', function () {
                if (!pendingForm || isSubmitting) return;

                const form = pendingForm;

                isSubmitting = true;
                confirmButton.disabled = true;
                confirmButton.textContent = 'Memproses...';

                form.dataset.confirmApproved = 'true';

                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');

                pendingForm = null;

                form.requestSubmit();
            });

            closeButtons.forEach(button => {
                button.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        })();
    </script>

</body>
</html>

<header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 shadow-sm">
    <div class="flex items-center space-x-3">
        <!-- Mobile menu hamburger -->
        <button type="button" class="md:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg hover:bg-slate-100" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
        <div class="flex items-center space-x-2">
            <span class="text-sm font-semibold text-slate-500">TokoKu</span>
            <span class="text-slate-300">/</span>
            <h1 class="text-base font-bold text-slate-800">@yield('title', 'Dashboard')</h1>
        </div>
    </div>

    <div class="flex items-center space-x-3 sm:space-x-4">
        <!-- Shortcut POS for staff/owner -->
        @if(auth()->check() && (auth()->user()->isOwner() || auth()->user()->isStaff()))
            <a href="{{ route('pos.index') }}" class="hidden sm:inline-flex items-center px-3.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition">
                <i class="fa-solid fa-cash-register mr-1.5"></i> Kasir POS
            </a>
        @endif

        <a href="{{ route('catalog.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
            <i class="fa-solid fa-store mr-1.5 text-indigo-600"></i> Katalog
        </a>

        <!-- Cart shortcut for users -->
        <a href="{{ route('cart.index') }}" class="relative p-2 text-slate-600 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition">
            <i class="fa-solid fa-cart-shopping text-lg"></i>
            @php $cartCount = count(session('cart', [])); @endphp
            @if($cartCount > 0)
                <span class="absolute top-1 right-1 w-4 h-4 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                    {{ $cartCount }}
                </span>
            @endif
        </a>

        <!-- User Dropdown Menu -->
        <div class="flex items-center space-x-3 border-l border-slate-200 pl-3 sm:pl-4">
            <div class="hidden sm:block text-right">
                <div class="text-xs font-bold text-slate-800">{{ auth()->user()->name }}</div>
                <div class="text-[11px] font-medium text-slate-500">
                    @if(auth()->user()->isOwner())
                        <span class="text-amber-600 font-bold">Owner</span>
                    @elseif(auth()->user()->isStaff())
                        <span class="text-blue-600 font-bold">Staff / Admin</span>
                    @else
                        <span class="text-emerald-600 font-bold">Pembeli</span>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</header>

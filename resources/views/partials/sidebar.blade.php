<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-300 flex flex-col transition-transform duration-300 transform -translate-x-full md:translate-x-0 shadow-2xl">
    <!-- Brand / Logo -->
    <div class="h-16 flex items-center justify-between px-6 bg-slate-950 border-b border-slate-800">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
            <div class="w-9 h-9 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-indigo-500/30 group-hover:bg-indigo-500 transition">
                <i class="fa-solid fa-store"></i>
            </div>
            <div>
                <span class="text-xl font-extrabold text-white tracking-tight">Toko<span class="text-indigo-400">Ku</span></span>
                <span class="block text-[10px] text-slate-400 uppercase tracking-widest font-semibold">Retail & Inventory</span>
            </div>
        </a>
        <button type="button" class="md:hidden text-slate-400 hover:text-white" onclick="document.getElementById('sidebar').classList.add('-translate-x-full')">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>

    <!-- User Profile Snippet in Sidebar -->
    <div class="px-5 py-4 border-b border-slate-800/80 bg-slate-900/60 flex items-center space-x-3">
        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-600 to-violet-500 text-white font-bold flex items-center justify-center text-sm shadow">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
            <div class="flex items-center mt-0.5">
                @if(auth()->user()->isOwner())
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        <i class="fa-solid fa-crown text-[9px] mr-1"></i> OWNER
                    </span>
                @elseif(auth()->user()->isStaff())
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                        <i class="fa-solid fa-user-tie text-[9px] mr-1"></i> STAFF/KASIR
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        <i class="fa-solid fa-user text-[9px] mr-1"></i> PELANGGAN
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Navigation Menus -->
    <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <!-- Dashboard Link -->
        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
            <i class="fa-solid fa-chart-pie w-5 text-center mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
            <span>Dashboard</span>
        </a>

        @if(auth()->user()->isOwner() || auth()->user()->isStaff())
            <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                Operasional Toko
            </div>

            <!-- POS Kasir -->
            <a href="{{ route('pos.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('pos.*') ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                <i class="fa-solid fa-cash-register w-5 text-center mr-3 {{ request()->routeIs('pos.*') ? 'text-white' : 'text-emerald-400' }}"></i>
                <span class="flex-1">POS Kasir</span>
                <span class="px-1.5 py-0.5 text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded font-semibold">Cepat</span>
            </a>

            <!-- Stok Masuk -->
            <a href="{{ route('purchases.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('purchases.*') ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                <i class="fa-solid fa-boxes-packing w-5 text-center mr-3 {{ request()->routeIs('purchases.*') ? 'text-white' : 'text-sky-400' }}"></i>
                <span>Stok Masuk (Beli)</span>
            </a>

            <!-- Penjualan -->
            <a href="{{ route('sales.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('sales.*') ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                <i class="fa-solid fa-receipt w-5 text-center mr-3 {{ request()->routeIs('sales.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Riwayat Penjualan</span>
            </a>

            <!-- Mutasi Stok -->
            <a href="{{ route('stock-movements.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('stock-movements.*') ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                <i class="fa-solid fa-clock-rotate-left w-5 text-center mr-3 {{ request()->routeIs('stock-movements.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Mutasi Stok (Audit)</span>
            </a>

            <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                Data Master
            </div>

            <!-- Produk -->
            <a href="{{ route('products.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('products.*') ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                <i class="fa-solid fa-box w-5 text-center mr-3 {{ request()->routeIs('products.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Produk</span>
            </a>

            <!-- Kategori -->
            <a href="{{ route('categories.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('categories.*') ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                <i class="fa-solid fa-tags w-5 text-center mr-3 {{ request()->routeIs('categories.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Kategori</span>
            </a>

            <!-- Pemasok -->
            <a href="{{ route('suppliers.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('suppliers.*') ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                <i class="fa-solid fa-truck-field w-5 text-center mr-3 {{ request()->routeIs('suppliers.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Pemasok</span>
            </a>

            <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                Laporan
            </div>

            <a href="{{ route('reports.sales') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('reports.sales') ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                <i class="fa-solid fa-file-invoice-dollar w-5 text-center mr-3 {{ request()->routeIs('reports.sales') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Laporan Penjualan</span>
            </a>

            <a href="{{ route('reports.purchases') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('reports.purchases') ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                <i class="fa-solid fa-file-lines w-5 text-center mr-3 {{ request()->routeIs('reports.purchases') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Laporan Pembelian</span>
            </a>

            <a href="{{ route('reports.stock') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('reports.stock') ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                <i class="fa-solid fa-warehouse w-5 text-center mr-3 {{ request()->routeIs('reports.stock') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Laporan Nilai Stok</span>
            </a>

            @if(auth()->user()->isOwner())
                <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    Sistem & Hak Akses
                </div>

                <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                    <i class="fa-solid fa-users-gear w-5 text-center mr-3 {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Kelola Pengguna</span>
                </a>
            @endif
        @else
            <!-- Pelanggan / User Menu -->
            <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                Menu Pelanggan
            </div>

            <a href="{{ route('catalog.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 text-slate-300 hover:text-white transition">
                <i class="fa-solid fa-bag-shopping w-5 text-center mr-3 text-indigo-400"></i>
                <span>Katalog Produk</span>
            </a>

            <a href="{{ route('cart.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 text-slate-300 hover:text-white transition">
                <i class="fa-solid fa-cart-shopping w-5 text-center mr-3 text-emerald-400"></i>
                <span>Keranjang Belanja</span>
            </a>

            <a href="{{ route('sales.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('sales.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                <i class="fa-solid fa-clock-rotate-left w-5 text-center mr-3 text-slate-400"></i>
                <span>Riwayat Belanja</span>
            </a>
        @endif
    </div>

    <!-- Bottom Actions -->
    <div class="p-4 border-t border-slate-800 bg-slate-950 space-y-2">
        <a href="{{ route('catalog.index') }}" target="_blank" class="flex items-center justify-center w-full px-3 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition">
            <i class="fa-solid fa-globe mr-2"></i> Buka Katalog Publik
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center justify-center w-full px-3 py-2 rounded-lg text-xs font-semibold bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 transition">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Keluar (Logout)
            </button>
        </form>
    </div>
</aside>

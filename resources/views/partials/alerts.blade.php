@if(session('success'))
    <div class="mb-4 flex items-center p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm">
        <div class="flex-shrink-0 text-emerald-500 mr-3">
            <i class="fa-solid fa-circle-check text-xl"></i>
        </div>
        <div class="flex-1 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-700 ml-3">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 flex items-center p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg shadow-sm">
        <div class="flex-shrink-0 text-rose-500 mr-3">
            <i class="fa-solid fa-circle-exclamation text-xl"></i>
        </div>
        <div class="flex-1 text-sm font-medium text-rose-800">
            {{ session('error') }}
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-700 ml-3">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

@if(session('warning'))
    <div class="mb-4 flex items-center p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r-lg shadow-sm">
        <div class="flex-shrink-0 text-amber-500 mr-3">
            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
        </div>
        <div class="flex-1 text-sm font-medium text-amber-800">
            {{ session('warning') }}
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-amber-400 hover:text-amber-700 ml-3">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

@if($errors->any())
    <div class="mb-4 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg shadow-sm">
        <div class="flex items-center mb-2">
            <i class="fa-solid fa-circle-xmark text-rose-500 mr-2"></i>
            <span class="text-sm font-bold text-rose-800">Terdapat beberapa kesalahan input:</span>
        </div>
        <ul class="list-disc list-inside text-sm text-rose-700 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

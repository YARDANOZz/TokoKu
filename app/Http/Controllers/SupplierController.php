<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('purchases');

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $suppliers = $query->latest()->paginate(10)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        $supplier = Supplier::create($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', "Pemasok '{$supplier->name}' berhasil ditambahkan.");
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', "Data pemasok '{$supplier->name}' berhasil diperbarui.");
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->count() > 0) {
            return back()->with('error', "Pemasok '{$supplier->name}' tidak dapat dihapus karena memiliki riwayat stok masuk.");
        }

        $name = $supplier->name;
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', "Pemasok '{$name}' berhasil dihapus.");
    }
}

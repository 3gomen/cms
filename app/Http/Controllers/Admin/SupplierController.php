<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::query()
            ->when(request('search'), function ($query, $searchQuery) {
                $query->where('name', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('website', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('email', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('contact_name', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('tel', 'LIKE', "%{$searchQuery}%");
            })
            ->latest()
            ->paginate();

        return $suppliers;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'website' => 'required',
            'email' => 'required|email',
            'contact_name' => 'required',
            'tel' => 'required',
        ]);

        return Supplier::create($request->all());
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required',
            'website' => 'required',
            'email' => 'required|email',
            'contact_name' => 'required',
            'tel' => 'required',
        ]);

        $supplier->update($request->all());

        return $supplier;
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return response()->noContent();
    }

    public function bulkDelete(Request $request)
    {
        Supplier::whereIn('id', $request->input('ids'))->delete();

        return response()->json(['message' => 'Suppliers deleted successfully']);
    }
}

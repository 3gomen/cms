<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accessory;
use Illuminate\Http\Request;

class AccessoryController extends Controller
{
    public function index()
{
    $accessories = Accessory::with('supplier')
        ->when(request('search'), function ($query, $searchQuery) {
            $query->where('category', 'LIKE', "%{$searchQuery}%")
                ->orWhere('brand', 'LIKE', "%{$searchQuery}%")
                ->orWhere('type', 'LIKE', "%{$searchQuery}%")
                ->orWhere('color', 'LIKE', "%{$searchQuery}%")
                ->orWhereHas('supplier', function ($query) use ($searchQuery) {
                    $query->where('name', 'LIKE', "%{$searchQuery}%");
                })
                ->orWhere('comment', 'LIKE', "%{$searchQuery}%");
        })
        ->latest()
        ->paginate();

    return $accessories;
}

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required',
            'brand' => 'required',
            'type' => 'required',
            'color' => 'required',
            'supplier_id' => 'required',
            'guarantee' => 'required',
            'price' => 'required',
            'sell_price' => 'required',
            'has_storage' => 'required',
            'comment' => 'required',
            'created_at' => 'required',
        ]);

        return Accessory::create($request->all());
    }

    public function update(Request $request, Accessory $accessory)
    {
        $request->validate([
            'category' => 'required',
            'brand' => 'required',
            'type' => 'required',
            'color' => 'required',
            'supplier_id' => 'required',
            'guarantee' => 'required',
            'price' => 'required',
            'sell_price' => 'required',
            'has_storage' => 'required',
            'comment' => 'required',
            'created_at' => 'required',
        ]);

        $accessory->update($request->all());

        return $accessory;
    }

    public function destroy(Accessory $accessory)
    {
        $accessory->delete();

        return response()->noContent();
    }

    public function bulkDelete(Request $request)
    {
        Accessory::whereIn('id', $request->input('ids'))->delete();

        return response()->json(['message' => 'Accessories deleted successfully']);
    }
}

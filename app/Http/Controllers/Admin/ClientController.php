<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::when(request('search'), function ($query, $searchQuery) {
                $query->where('name', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('tel', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('email', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('address', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('birthinfo', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('idcard', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('comment', 'LIKE', "%{$searchQuery}%");
            })
            ->latest()
            ->paginate();

        return $clients;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        return Client::create($request->all());
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $client->update($request->all());

        return $client;
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return response()->noContent();
    }

    public function bulkDelete(Request $request)
    {
        Client::whereIn('id', $request->input('ids'))->delete();

        return response()->json(['message' => 'Clients deleted successfully']);
    }
}

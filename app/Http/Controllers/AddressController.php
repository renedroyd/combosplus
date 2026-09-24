<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->get();

        return view('addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('addresses.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Address::class);

        $data = $request->validate([
            'alias' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        $data['user_id'] = $request->user()->getAuthIdentifier();
        $data['country'] = $data['country'] ?? 'Chile';

        if ($data['is_default'] ?? false) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        Address::create($data);

        return redirect()->route('addresses.index')->with('success', 'Dirección guardada.');
    }

    public function edit(Address $address)
    {
        $this->authorize('update', $address);

        return view('addresses.edit', compact('address'));
    }

    public function update(Request $request, Address $address)
    {
        $this->authorize('update', $address);

        $data = $request->validate([
            'alias' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        if ($data['is_default'] ?? false) {
            $request->user()->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect()->route('addresses.index')->with('success', 'Dirección actualizada.');
    }

    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);
        $address->delete();

        return redirect()->route('addresses.index')->with('success', 'Dirección eliminada.');
    }
}

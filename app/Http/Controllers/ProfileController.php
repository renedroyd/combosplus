<?php

namespace App\Http\Controllers;

use App\Services\Tenancy\TenantCustomerProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct(
        private readonly TenantCustomerProvisioner $customerProvisioner,
    ) {
    }

    public function show()
    {
        $user = auth()->user();
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();

        return view('profile.show', compact('user', 'addresses'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique(($user->getConnectionName() ?: config('database.default')) . '.users')->ignore($user->getAuthIdentifier()),
            ],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        // Keep the tenant-local operational customer projection aligned with
        // the central platform identity while the staged migration is active.
        $this->customerProvisioner->sync($user);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}

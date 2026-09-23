<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PlatformUser;
use App\Models\TenantMembership;
use App\Services\Tenancy\TenantCustomerProvisioner;
use App\Services\Tenancy\TenantAccessService;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct(
        private readonly TenantAccessService $tenantAccess,
        private readonly TenantCustomerProvisioner $customerProvisioner,
    ) {
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (PlatformUser::query()->where('email', $request->string('email'))->exists()) {
            return back()->withErrors([
                'email' => 'Ya existe una cuenta con este correo electrónico.',
            ])->onlyInput('email');
        }

        $user = PlatformUser::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'password' => Hash::make($request->string('password')),
        ]);

        if (tenant()) {
            TenantMembership::create([
                'tenant_id' => tenant()->getTenantKey(),
                'user_id' => $user->getAuthIdentifier(),
                'role' => 'customer',
                'status' => 'active',
                'is_owner' => false,
            ]);

            $this->customerProvisioner->ensure($user);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $this->migrateGuestCart($user);

        if (session()->pull('intended_checkout', false)) {
            return redirect()->route('checkout.index');
        }

        return redirect()->intended('/');
    }

    private function migrateGuestCart($user): void
    {
        $sessionId = session()->get('cart_session_id');

        if (! $sessionId) {
            return;
        }

        $guestCart = Cart::where('session_id', $sessionId)->first();

        if (! $guestCart) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($guestCart->items as $item) {
            $existing = $userCart->items()->where('product_id', $item->product_id)->first();

            if ($existing) {
                $existing->quantity += $item->quantity;
                $existing->save();
            } else {
                $userCart->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
            }
        }

        $guestCart->delete();
        session()->forget('cart_session_id');
    }
}

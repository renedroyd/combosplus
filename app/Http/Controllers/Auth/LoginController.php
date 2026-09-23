<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\Tenancy\TenantCustomerProvisioner;
use App\Services\Tenancy\TenantAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(
        private readonly TenantAccessService $tenantAccess,
        private readonly TenantCustomerProvisioner $customerProvisioner,
    ) {
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if (tenant()) {
            if (! $this->tenantAccess->canAccessCurrentTenant($user)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                abort(403, 'No tienes acceso a este negocio.');
            }

            $this->customerProvisioner->ensure($user);
        }

        $this->migrateGuestCart($user);

        if (session()->pull('intended_checkout', false)) {
            return redirect()->route('checkout.index');
        }

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
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

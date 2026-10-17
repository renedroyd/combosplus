<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\SaasSubscriptionPayment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriptionPaymentReceiptController extends Controller
{
    public function __invoke(SaasSubscriptionPayment $payment): StreamedResponse
    {
        abort_unless(filled($payment->receipt_path), 404);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($payment->receipt_path), 404);

        return $disk->download($payment->receipt_path);
    }
}

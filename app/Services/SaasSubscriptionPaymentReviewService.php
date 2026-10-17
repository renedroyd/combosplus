<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\SaasSubscriptionPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaasSubscriptionPaymentReviewService
{
    public function approve(int|SaasSubscriptionPayment $payment, int|Admin $reviewer): SaasSubscriptionPayment
    {
        return $this->review($payment, $reviewer, SaasSubscriptionPayment::STATUS_APPROVED, null);
    }

    public function reject(int|SaasSubscriptionPayment $payment, int|Admin $reviewer, string $reason): SaasSubscriptionPayment
    {
        $reason = trim($reason);

        if ($reason === '') {
            throw ValidationException::withMessages(['rejection_reason' => 'Indica el motivo del rechazo.']);
        }

        return $this->review($payment, $reviewer, SaasSubscriptionPayment::STATUS_REJECTED, $reason);
    }

    private function review(
        int|SaasSubscriptionPayment $payment,
        int|Admin $reviewer,
        string $status,
        ?string $reason,
    ): SaasSubscriptionPayment {
        $paymentId = $payment instanceof SaasSubscriptionPayment ? $payment->getKey() : $payment;
        $reviewerId = $reviewer instanceof Admin ? $reviewer->getKey() : $reviewer;

        return DB::connection((new SaasSubscriptionPayment())->getConnectionName())
            ->transaction(function () use ($paymentId, $reviewerId, $status, $reason): SaasSubscriptionPayment {
                $record = SaasSubscriptionPayment::query()->lockForUpdate()->findOrFail($paymentId);

                if (! $record->isPending()) {
                    throw ValidationException::withMessages([
                        'status' => 'Este pago ya fue revisado y no puede volver a procesarse.',
                    ]);
                }

                $record->forceFill([
                    'status' => $status,
                    'reviewed_by_admin_id' => $reviewerId,
                    'reviewed_at' => now(),
                    'rejection_reason' => $reason,
                ])->save();

                return $record->refresh();
            });
    }

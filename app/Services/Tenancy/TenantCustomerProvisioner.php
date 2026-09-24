<?php

namespace App\Services\Tenancy;

use App\Models\PlatformUser;
use App\Models\User;
use RuntimeException;

class TenantCustomerProvisioner
{
    public function ensure(PlatformUser $platformUser): User
    {
        if (! tenant()) {
            throw new RuntimeException('Tenant context is required to provision a tenant customer.');
        }

        $customer = User::query()->find($platformUser->getAuthIdentifier());

        if ($customer) {
            $this->assertCompatible($customer, $platformUser);

            return $customer;
        }

        return User::query()->forceCreate($this->attributesFor($platformUser));
    }

    public function sync(PlatformUser $platformUser): User
    {
        $customer = $this->ensure($platformUser);

        $customer->forceFill($this->attributesFor($platformUser))->save();

        return $customer->refresh();
    }

    private function assertCompatible(User $customer, PlatformUser $platformUser): void
    {
        if (strcasecmp((string) $customer->email, (string) $platformUser->email) !== 0) {
            throw new RuntimeException('Tenant customer identity conflicts with the platform identity.');
        }
    }

    private function attributesFor(PlatformUser $platformUser): array
    {
        return [
            'id' => $platformUser->getAuthIdentifier(),
            'name' => $platformUser->name,
            'email' => $platformUser->email,
            'password' => $platformUser->getAuthPassword(),
            'telegram_chat_id' => $platformUser->telegram_chat_id,
        ];
    }
}

<?php

namespace Cratespace\Sentinel\Actions;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Auth\User;
use Cratespace\Sentinel\Codes\RecoveryCode;
use Cratespace\Sentinel\Events\TwoFactorAuthenticationEnabled;
use Cratespace\Sentinel\Contracts\Providers\TwoFactorAuthenticationProvider;

class EnableTwoFactorAuthentication
{
    /**
     * Default number of recovery codes to be generated.
     *
     * @var int
     */
    protected $numberOfCodes = 8;

    /**
     * The two factor authentication provider.
     *
     * @var \Sentinel\Contracts\Providers\TwoFactorAuthenticationProvider
     */
    protected $provider;

    /**
     * Create a new action instance.
     *
     * @param \Sentinel\Contracts\Providers\TwoFactorAuthenticationProvider $provider
     *
     * @return void
     */
    public function __construct(TwoFactorAuthenticationProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Enable two factor authentication for the user.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     *
     * @return void
     */
    public function __invoke(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => encrypt($this->provider->generateSecretKey()),
            'two_factor_recovery_codes' => $this->generateRecoveryCode(),
        ])->save();

        TwoFactorAuthenticationEnabled::dispatch($user);
    }

    /**
     * Generate new recovery codes for user.
     *
     * @return string
     */
    protected function generateRecoveryCode(): string
    {
        return encrypt(json_encode(
            Collection::times(
                $this->numberOfCodes,
                fn () => RecoveryCode::generate()
            )->all()
        ));
    }
}

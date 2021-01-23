<?php

namespace Citadel\Providers;

use PragmaRX\Google2FA\Google2FA;
use Citadel\Contracts\Providers\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;

class TwoFactorAuthenticationProvider implements TwoFactorAuthenticationProviderContract
{
    /**
     * The underlying library providing two factor authentication helper services.
     *
     * @var \PragmaRX\Google2FA\Google2FA
     */
    protected $engine;

    /**
     * Create a new two factor authentication provider instance.
     *
     * @param \PragmaRX\Google2FA\Google2FA $engine
     *
     * @return void
     */
    public function __construct(Google2FA $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Generate a new secret key.
     *
     * @return string
     */
    public function generateSecretKey(): string
    {
        return $this->engine->generateSecretKey();
    }

    /**
     * Get the two factor authentication QR code URL.
     *
     * @param string $companyName
     * @param string $companyEmail
     * @param string $secret
     *
     * @return string
     */
    public function qrCodeUrl(string $companyName, string $companyEmail, string $secret): string
    {
        return $this->engine->getQRCodeUrl($companyName, $companyEmail, $secret);
    }

    /**
     * Verify the given code.
     *
     * @param string $secret
     * @param string $code
     *
     * @return bool
     */
    public function verify(string $secret, string $code): bool
    {
        return $this->engine->verifyKey($secret, $code);
    }
}

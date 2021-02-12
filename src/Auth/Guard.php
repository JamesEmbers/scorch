<?php

namespace Cratespace\Sentinel\Auth;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Cratespace\Sentinel\Sentinel\Config;
use Cratespace\Sentinel\Auth\Tokens\TransientToken;
use Cratespace\Sentinel\Models\Traits\HasApiTokens;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Cratespace\Sentinel\Auth\Tokens\PersonalAccessToken;

class Guard
{
    /**
     * The authentication factory implementation.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * The number of minutes tokens should be allowed to remain valid.
     *
     * @var int
     */
    protected $expiration;

    /**
     * The provider name.
     *
     * @var string
     */
    protected $provider;

    /**
     * Create a new guard instance.
     *
     * @param \Illuminate\Contracts\Auth\Factory $auth
     * @param int                                $expiration
     * @param string                             $provider
     *
     * @return void
     */
    public function __construct(AuthFactory $auth, ?int $expiration = null, ?string $provider = null)
    {
        $this->auth = $auth;
        $this->expiration = $expiration;
        $this->provider = $provider;
    }

    /**
     * Retrieve the authenticated user for the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        if ($user = $this->auth->guard(Config::guard(['web']))->user()) {
            return $this->supportsTokens($user)
                ? $user->withAccessToken(new TransientToken())
                : $user;
        }

        if ($token = $request->bearerToken()) {
            $accessToken = PersonalAccessToken::findToken($token);

            if (! $accessToken ||
                ($this->expiration &&
                 $accessToken->created_at->lte(now()->subMinutes($this->expiration))) ||
                ! $this->hasValidProvider($accessToken->tokenable)) {
                return;
            }

            return $this->supportsTokens($accessToken->tokenable) ? $accessToken->tokenable->withAccessToken(
                tap($accessToken->forceFill(['last_used_at' => now()]))->save()
            ) : null;
        }
    }

    /**
     * Determine if the tokenable model supports API tokens.
     *
     * @param mixed $tokenable
     *
     * @return bool
     */
    protected function supportsTokens($tokenable = null): bool
    {
        return $tokenable && in_array(HasApiTokens::class, class_uses_recursive(
            get_class($tokenable)
        ));
    }

    /**
     * Determine if the tokenable model matches the provider's model type.
     *
     * @param \Illuminate\Database\Eloquent\Model $tokenable
     *
     * @return bool
     */
    protected function hasValidProvider(Model $tokenable): bool
    {
        if (is_null($this->provider)) {
            return true;
        }

        $model = config("auth.providers.{$this->provider}.model");

        return $tokenable instanceof $model;
    }
}
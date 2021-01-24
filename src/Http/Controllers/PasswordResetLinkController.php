<?php

namespace Citadel\Http\Controllers;

use Citadel\Citadel\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;
use Citadel\Http\Requests\PasswordResetLinkRequest;
use Citadel\Http\Responses\FailedPasswordResetLinkRequestResponse;
use Citadel\Contracts\Responses\RequestPasswordResetLinkViewResponse;
use Citadel\Http\Responses\SuccessfulPasswordResetLinkRequestResponse;

class PasswordResetLinkController extends Controller
{
    /**
     * Instance of the password broker implementation.
     *
     * @var \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected $broker;

    /**
     * Create a new controller instance.
     *
     * @param \Illuminate\Contracts\Auth\PasswordBroker $broker
     *
     * @return void
     */
    public function __construct(PasswordBroker $broker)
    {
        $this->broker = $broker;
    }

    /**
     * Show the reset password link request view.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, RequestPasswordResetLinkViewResponse $response): Response
    {
        return $response->toResponse($request);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function store(PasswordResetLinkRequest $request): Responsable
    {
        $status = $this->broker->sendResetLink($request->only(Config::email()));

        return $status == Password::RESET_LINK_SENT
            ? $this->app(SuccessfulPasswordResetLinkRequestResponse::class, ['status' => $status])
            : $this->app(FailedPasswordResetLinkRequestResponse::class, ['status' => $status]);
    }
}
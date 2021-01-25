<?php

namespace Cratespace\Citadel\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class TwoFactorChallengeResponse extends Response implements Responsable
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return $request->wantsJson()
            ? $this->json(['two_factor' => true])
            : $this->redirectToRoute('two-factor.login');
    }
}

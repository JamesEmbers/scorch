<?php

namespace Cratespace\Sentinel\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class RegisterResponse extends Response implements Responsable
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
        return $request->expectsJson()
            ? $this->json()
            : $this->redirectToIntended($this->home());
    }
}

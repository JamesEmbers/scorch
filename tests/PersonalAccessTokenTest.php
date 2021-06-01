<?php

namespace Emberfuse\Scorch\Tests;

use PHPUnit\Framework\TestCase;
use Emberfuse\Scorch\API\Tokens\PersonalAccessToken;

class PersonalAccessTokenTest extends TestCase
{
    public function testCanDetermineWhatItCanAndCantDo()
    {
        $token = new PersonalAccessToken();

        $token->abilities = [];

        $this->assertFalse($token->can('foo'));

        $token->abilities = ['foo'];

        $this->assertTrue($token->can('foo'));
        $this->assertFalse($token->can('bar'));
        $this->assertTrue($token->cant('bar'));
        $this->assertFalse($token->cant('foo'));

        $token->abilities = ['foo', '*'];

        $this->assertTrue($token->can('foo'));
        $this->assertTrue($token->can('bar'));
    }
}

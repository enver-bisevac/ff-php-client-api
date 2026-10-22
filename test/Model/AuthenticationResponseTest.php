<?php
/**
 * AuthenticationResponseTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\AuthenticationResponse;

/**
 * @covers \OpenAPI\Client\Model\AuthenticationResponse
 */
class AuthenticationResponseTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return AuthenticationResponse::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['auth_token'];

        $empty = new AuthenticationResponse();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

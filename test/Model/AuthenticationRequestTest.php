<?php
/**
 * AuthenticationRequestTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\AuthenticationRequest;

/**
 * @covers \OpenAPI\Client\Model\AuthenticationRequest
 */
class AuthenticationRequestTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return AuthenticationRequest::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['api_key'];

        $empty = new AuthenticationRequest();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

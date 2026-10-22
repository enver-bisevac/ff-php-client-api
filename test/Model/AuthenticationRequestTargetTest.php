<?php
/**
 * AuthenticationRequestTargetTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\AuthenticationRequestTarget;

/**
 * @covers \OpenAPI\Client\Model\AuthenticationRequestTarget
 */
class AuthenticationRequestTargetTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return AuthenticationRequestTarget::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['identifier'];

        $empty = new AuthenticationRequestTarget();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

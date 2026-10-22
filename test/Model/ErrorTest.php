<?php
/**
 * ErrorTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\Error;

/**
 * @covers \OpenAPI\Client\Model\Error
 */
class ErrorTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return Error::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['code', 'message'];

        $empty = new Error();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

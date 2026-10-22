<?php
/**
 * ServeTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\Serve;

/**
 * @covers \OpenAPI\Client\Model\Serve
 */
class ServeTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return Serve::class;
    }

    public function testRequiredProperties(): void
    {
        $required = [];

        $empty = new Serve();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

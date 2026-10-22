<?php
/**
 * ClauseTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\Clause;

/**
 * @covers \OpenAPI\Client\Model\Clause
 */
class ClauseTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return Clause::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['attribute', 'op', 'values', 'negate'];

        $empty = new Clause();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

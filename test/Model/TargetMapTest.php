<?php
/**
 * TargetMapTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\TargetMap;

/**
 * @covers \OpenAPI\Client\Model\TargetMap
 */
class TargetMapTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return TargetMap::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['identifier', 'name'];

        $empty = new TargetMap();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

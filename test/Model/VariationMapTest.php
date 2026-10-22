<?php
/**
 * VariationMapTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\VariationMap;

/**
 * @covers \OpenAPI\Client\Model\VariationMap
 */
class VariationMapTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return VariationMap::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['variation'];

        $empty = new VariationMap();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

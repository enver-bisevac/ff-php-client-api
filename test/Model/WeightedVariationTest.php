<?php
/**
 * WeightedVariationTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\WeightedVariation;

/**
 * @covers \OpenAPI\Client\Model\WeightedVariation
 */
class WeightedVariationTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return WeightedVariation::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['variation', 'weight'];

        $empty = new WeightedVariation();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

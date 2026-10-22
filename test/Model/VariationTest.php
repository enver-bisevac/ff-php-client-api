<?php
/**
 * VariationTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\Variation;

/**
 * @covers \OpenAPI\Client\Model\Variation
 */
class VariationTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return Variation::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['identifier', 'value'];

        $empty = new Variation();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

<?php
/**
 * TargetDataTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\TargetData;

/**
 * @covers \OpenAPI\Client\Model\TargetData
 */
class TargetDataTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return TargetData::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['identifier', 'name', 'attributes'];

        $empty = new TargetData();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

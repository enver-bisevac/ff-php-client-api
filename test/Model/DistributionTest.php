<?php
/**
 * DistributionTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\Distribution;

/**
 * @covers \OpenAPI\Client\Model\Distribution
 */
class DistributionTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return Distribution::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['bucket_by', 'variations'];

        $empty = new Distribution();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

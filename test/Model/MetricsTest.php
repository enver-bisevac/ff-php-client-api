<?php
/**
 * MetricsTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\Metrics;

/**
 * @covers \OpenAPI\Client\Model\Metrics
 */
class MetricsTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return Metrics::class;
    }

    public function testRequiredProperties(): void
    {
        $required = [];

        $empty = new Metrics();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

<?php
/**
 * MetricsDataTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\MetricsData;

/**
 * @covers \OpenAPI\Client\Model\MetricsData
 */
class MetricsDataTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return MetricsData::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['timestamp', 'count', 'metrics_type', 'attributes'];

        $empty = new MetricsData();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

<?php
/**
 * FeatureConfigTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\FeatureConfig;

/**
 * @covers \OpenAPI\Client\Model\FeatureConfig
 */
class FeatureConfigTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return FeatureConfig::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['project', 'environment', 'feature', 'state', 'kind', 'variations', 'default_serve', 'off_variation'];

        $empty = new FeatureConfig();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

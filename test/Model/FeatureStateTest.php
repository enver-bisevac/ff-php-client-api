<?php
/**
 * FeatureStateTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\FeatureState;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OpenAPI\Client\Model\FeatureState
 */
class FeatureStateTest extends TestCase
{
    public function testConstants(): void
    {
        $this->assertSame('on', FeatureState::ON);
        $this->assertSame('off', FeatureState::OFF);
    }

    public function testAllowableEnumValues(): void
    {
        $this->assertSame(['on', 'off'], FeatureState::getAllowableEnumValues());
    }
}

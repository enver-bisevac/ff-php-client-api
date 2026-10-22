<?php
/**
 * TargetTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\Target;

/**
 * @covers \OpenAPI\Client\Model\Target
 */
class TargetTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return Target::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['identifier', 'account', 'org', 'environment', 'project', 'name'];

        $empty = new Target();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

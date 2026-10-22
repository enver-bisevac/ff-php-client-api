<?php
/**
 * PrerequisiteTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\Prerequisite;

/**
 * @covers \OpenAPI\Client\Model\Prerequisite
 */
class PrerequisiteTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return Prerequisite::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['feature', 'variations'];

        $empty = new Prerequisite();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

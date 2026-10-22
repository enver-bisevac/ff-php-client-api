<?php
/**
 * SegmentTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\Segment;

/**
 * @covers \OpenAPI\Client\Model\Segment
 */
class SegmentTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return Segment::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['identifier', 'name'];

        $empty = new Segment();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

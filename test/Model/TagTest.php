<?php
/**
 * TagTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\Tag;

/**
 * @covers \OpenAPI\Client\Model\Tag
 */
class TagTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return Tag::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['name'];

        $empty = new Tag();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

<?php
/**
 * PaginationTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\Pagination;

/**
 * @covers \OpenAPI\Client\Model\Pagination
 */
class PaginationTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return Pagination::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['page_count', 'item_count', 'page_size', 'page_index'];

        $empty = new Pagination();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

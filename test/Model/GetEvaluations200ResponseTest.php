<?php
/**
 * GetEvaluations200ResponseTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\GetEvaluations200Response;

/**
 * @covers \OpenAPI\Client\Model\GetEvaluations200Response
 */
class GetEvaluations200ResponseTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return GetEvaluations200Response::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['page_count', 'item_count', 'page_size', 'page_index'];

        $empty = new GetEvaluations200Response();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

<?php
/**
 * EvaluationTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\Evaluation;

/**
 * @covers \OpenAPI\Client\Model\Evaluation
 */
class EvaluationTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return Evaluation::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['flag', 'value', 'kind'];

        $empty = new Evaluation();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

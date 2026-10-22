<?php
/**
 * ServingRuleTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\ServingRule;

/**
 * @covers \OpenAPI\Client\Model\ServingRule
 */
class ServingRuleTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return ServingRule::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['priority', 'clauses', 'serve'];

        $empty = new ServingRule();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

<?php
/**
 * KeyValueTest
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\KeyValue;

/**
 * @covers \OpenAPI\Client\Model\KeyValue
 */
class KeyValueTest extends ModelTestCase
{
    protected function modelClass(): string
    {
        return KeyValue::class;
    }

    public function testRequiredProperties(): void
    {
        $required = ['key', 'value'];

        $empty = new KeyValue();
        $this->assertSame(
            array_map(static fn ($p) => "'$p' can't be null", $required),
            $empty->listInvalidProperties()
        );
        $this->assertSame($required === [], $empty->valid());
    }
}

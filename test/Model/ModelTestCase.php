<?php
/**
 * ModelTestCase
 *
 * Shared behaviour checks for every generated model, driven by the model's own
 * openAPITypes() metadata.
 */

namespace OpenAPI\Client\Test\Model;

use OpenAPI\Client\Model\ModelInterface;
use OpenAPI\Client\ObjectSerializer;
use PHPUnit\Framework\TestCase;

abstract class ModelTestCase extends TestCase
{
    /**
     * @return class-string<ModelInterface>
     */
    abstract protected function modelClass(): string;

    /**
     * Builds a valid sample value for an OpenAPI type.
     *
     * @return mixed
     */
    protected function sampleValue(string $type, string $name, ?array $allowed = null, int $depth = 0)
    {
        if ($allowed) {
            return reset($allowed);
        }
        switch ($type) {
            case 'string':
                return "sample-$name";
            case 'int':
                return 42;
            case 'bool':
                return true;
            case 'object':
                return (object) ['key' => 'value'];
            case 'string[]':
                return ['a', 'b'];
        }
        if (substr($type, -2) === '[]') {
            $class = substr($type, 0, -2);
            // self-referencing models (e.g. Segment) would recurse forever
            return $depth >= self::MAX_DEPTH ? [] : [$this->sampleModel($class, $depth + 1), $this->sampleModel($class, $depth + 1)];
        }
        if (!method_exists($type, 'openAPITypes')) {
            // enum-like class holding only string constants (e.g. FeatureState)
            return array_values((new \ReflectionClass($type))->getConstants())[0];
        }
        return $this->sampleModel($type, $depth + 1);
    }

    private const MAX_DEPTH = 2;

    protected function sampleModel(string $class, int $depth = 0): ModelInterface
    {
        $data = [];
        foreach ($class::openAPITypes() as $property => $type) {
            $allowed = $this->allowedValues($class, $property);
            $data[$property] = $this->sampleValue($type, $property, $allowed, $depth);
        }
        return new $class($data);
    }

    protected function allowedValues(string $class, string $property): ?array
    {
        $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $property))) . 'AllowableValues';
        return method_exists($class, $method) ? (new $class())->$method() : null;
    }

    protected function sample(): ModelInterface
    {
        return $this->sampleModel($this->modelClass());
    }

    public function testModelMetadata(): void
    {
        $class = $this->modelClass();
        $model = new $class();

        $this->assertInstanceOf(ModelInterface::class, $model);
        $this->assertInstanceOf(\ArrayAccess::class, $model);
        $this->assertInstanceOf(\JsonSerializable::class, $model);
        $this->assertSame(array_keys($class::openAPITypes()), array_keys($class::attributeMap()));
        $this->assertSame(array_keys($class::openAPITypes()), array_keys($class::setters()));
        $this->assertSame(array_keys($class::openAPITypes()), array_keys($class::getters()));
        $this->assertNotEmpty($model->getModelName());
    }

    public function testNewModelHasNullProperties(): void
    {
        $class = $this->modelClass();
        $model = new $class();

        foreach ($class::getters() as $getter) {
            $this->assertNull($model->$getter());
        }
    }

    public function testSettersAndGetters(): void
    {
        $class = $this->modelClass();
        $model = new $class();

        foreach ($class::openAPITypes() as $property => $type) {
            $value = $this->sampleValue($type, $property, $this->allowedValues($class, $property));
            $setter = $class::setters()[$property];
            $getter = $class::getters()[$property];

            $this->assertSame($model, $model->$setter($value), "$property setter is fluent");
            $this->assertEquals($value, $model->$getter(), "$property round-trips");
            $this->assertEquals($value, $model[$property], "$property via ArrayAccess");
            $this->assertTrue(isset($model[$property]));
        }
    }

    public function testConstructorPopulatesProperties(): void
    {
        $class = $this->modelClass();
        $model = $this->sample();

        foreach ($class::getters() as $property => $getter) {
            $this->assertNotNull($model->$getter(), "$property populated by constructor");
        }
    }

    public function testSampleIsValid(): void
    {
        $model = $this->sample();

        $this->assertSame([], $model->listInvalidProperties());
        $this->assertTrue($model->valid());
    }

    public function testOffsetSetAndUnset(): void
    {
        $class = $this->modelClass();
        $model = new $class();
        $property = array_key_first($class::openAPITypes());
        $value = $this->sampleValue($class::openAPITypes()[$property], $property, $this->allowedValues($class, $property));

        $model[$property] = $value;
        $this->assertEquals($value, $model[$property]);

        unset($model[$property]);
        $this->assertFalse(isset($model[$property]));
    }

    public function testSerializationRoundTrip(): void
    {
        $class = $this->modelClass();
        $model = $this->sample();

        $json = json_encode(ObjectSerializer::sanitizeForSerialization($model));
        $this->assertNotFalse($json);

        $decoded = json_decode($json);
        $restored = ObjectSerializer::deserialize($decoded, $class);

        $this->assertInstanceOf($class, $restored);
        $this->assertEquals(
            json_decode((string) $model),
            json_decode((string) $restored)
        );
    }

    public function testJsonSerializeUsesAttributeNames(): void
    {
        $class = $this->modelClass();
        $json = json_decode(json_encode($this->sample()), true);

        foreach ($class::attributeMap() as $attribute) {
            $this->assertArrayHasKey($attribute, $json);
        }
    }
}

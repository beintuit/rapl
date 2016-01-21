<?php

namespace RAPL\Tests\Unit\Serializer;

use Mockery;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;
use RAPL\RAPL\Mapping\ClassMetadata;
use RAPL\RAPL\Serializer\Serializer;

class SerializerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MockInterface|ClassMetadata
     */
    private $classMetadata;

    /**
     * @var MockInterface|\RAPL\RAPL\UnitOfWork
     */
    private $unitOfWork;

    /**
     * @var MockInterface|\RAPL\RAPL\Mapping\ClassMetadataFactory
     */
    private $classMetadataFactory;

    /**
     * @var Serializer
     */
    private $serializer;

    protected function setUp()
    {
        $this->classMetadata = Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
        $this->classMetadata->shouldReceive('getName')->andReturn('Foo\Bar');
        $this->classMetadata->shouldReceive('getFormat')->andReturn('json');

        $this->unitOfWork           = Mockery::mock('RAPL\RAPL\UnitOfWork');
        $this->classMetadataFactory = Mockery::mock('RAPL\RAPL\Mapping\ClassMetadataFactory');

        $this->serializer = new Serializer($this->classMetadata, $this->unitOfWork, $this->classMetadataFactory);
    }

    /**
     * @param MockInterface $classMetadataMock
     * @param string        $fieldName
     * @param array         $mapping
     */
    private function addFieldMappingTo(MockInterface $classMetadataMock, $fieldName, array $mapping = [])
    {
        if (!isset($mapping['serializedName'])) {
            $mapping['serializedName'] = $fieldName;
        }
        if (!isset($mapping['type'])) {
            $mapping['type'] = 'string';
        }

        $classMetadataMock->shouldReceive('getFieldName')->with($mapping['serializedName'])->andReturn($fieldName);
        $classMetadataMock->shouldReceive('hasField')->with($fieldName)->andReturn(true);
        $classMetadataMock->shouldReceive('getFieldMapping')->with($fieldName)->andReturn($mapping);
    }

    /**
     * @param string $className
     * @param array  $data
     *
     * @return MockInterface
     */
    private function mockReturnEntity($className, array $data)
    {
        $entity = Mockery::mock($className);

        $this->unitOfWork->shouldReceive('createEntity')->with($className, $data)->once()->andReturn($entity);

        return $entity;
    }

    public function testDeserializeSimpleDataReturnsHydratedEntities()
    {
        $this->addFieldMappingTo(
            $this->classMetadata,
            'stringData',
            [
                'serializedName' => 'string',
                'type'           => 'string',
            ]
        );

        $returnedEntity  = $this->mockReturnEntity('Foo\Bar', ['stringData' => 'Foo Bar']);
        $returnedEntity2 = $this->mockReturnEntity('Foo\Bar', ['stringData' => 'Bar Baz']);

        $json = '[
            {
                "string": "Foo Bar"
            },
            {
                "string": "Bar Baz"
            }
        ]';

        $entities = $this->serializer->deserialize($json, true);

        $this->assertSame(2, count($entities));
        $this->assertSame($returnedEntity, $entities[0]);
        $this->assertSame($returnedEntity2, $entities[1]);
    }

    public function testDeserializeSingleEntityReturnsArrayOfEntities()
    {
        $this->addFieldMappingTo(
            $this->classMetadata,
            'stringData',
            ['serializedName' => 'string', 'type' => 'string']
        );

        $returnedEntity = $this->mockReturnEntity('Foo\Bar', ['stringData' => 'Foo Bar']);

        $json = '{
            "string": "Foo Bar"
        }';

        $result = $this->serializer->deserialize($json, false);

        $this->assertSame(1, count($result));
        $this->assertSame($returnedEntity, $result[0]);
    }

    public function testDeserializeWrappedDataUnwrapsTheDataBeforeDeserializing()
    {
        $this->addFieldMappingTo(
            $this->classMetadata,
            'stringData',
            [
                'serializedName' => 'string',
                'type'           => 'string',
            ]
        );

        $returnedEntity = $this->mockReturnEntity('Foo\Bar', ['stringData' => 'Foo Bar']);

        $json = '{"results": [{
            "string": "Foo Bar"
        }]}';

        $entities = $this->serializer->deserialize($json, true, ['results']);

        $this->assertSame(1, count($entities));
        $this->assertSame($returnedEntity, $entities[0]);
    }

    public function testDeserializeWithNonExistingEnvelopesReturnsEmptyArray()
    {
        $this->addFieldMappingTo($this->classMetadata, 'foo');

        $json = '{"results": [{
            "foo": "bar"
        }]}';

        $result = $this->serializer->deserialize($json, true, ['envelope']);

        $this->assertEmpty($result);
    }

    public function testDeserializeComplexDataIncludesAssociatedEntities()
    {
        $this->addFieldMappingTo($this->classMetadata, 'string');
        $this->addFieldMappingTo(
            $this->classMetadata,
            'embedded',
            [
                'serializedName' => 'assoc',
                'embedded'       => true,
                'type'           => 'one',
                'association'    => ClassMetadata::EMBED_ONE,
                'targetEntity'   => 'Foo\BarBaz',
            ]
        );

        $subClassMetadata = Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
        $subClassMetadata->shouldReceive('getName')->andReturn('Foo\BarBaz');
        $subClassMetadata->shouldReceive('getFormat')->andReturn('json');

        $this->addFieldMappingTo($subClassMetadata, 'foo');

        $subEntity  = $this->mockReturnEntity('Foo\BarBaz', ['foo' => 'bar']);
        $mainEntity = $this->mockReturnEntity('Foo\Bar', ['string' => 'foo', 'embedded' => $subEntity]);

        $this->classMetadataFactory->shouldReceive('getMetadataFor')->andReturn($subClassMetadata);

        $json = '[{
            "string": "foo",
            "assoc": {
                "foo": "bar"
            }
        }]';

        $entities = $this->serializer->deserialize($json, true);

        $this->assertSame(1, count($entities));
        $this->assertSame($mainEntity, $entities[0]);
    }

    public function testDeserializeWithMissingAssociation()
    {
        $this->addFieldMappingTo($this->classMetadata, 'string');
        $this->addFieldMappingTo(
            $this->classMetadata,
            'embedded',
            [
                'serializedName' => 'assoc',
                'embedded'       => true,
                'type'           => 'one',
                'association'    => ClassMetadata::EMBED_ONE,
                'targetEntity'   => 'Foo\BarBaz',
            ]
        );

        $subClassMetadata = Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
        $subClassMetadata->shouldReceive('getName')->andReturn('Foo\BarBaz');
        $subClassMetadata->shouldReceive('getFormat')->andReturn('json');

        $this->addFieldMappingTo($subClassMetadata, 'foo');

        $mainEntity = $this->mockReturnEntity('Foo\Bar', ['string' => 'foo', 'embedded' => null]);

        $this->classMetadataFactory->shouldReceive('getMetadataFor')->andReturn($subClassMetadata);

        $json = '[{
            "string": "foo",
            "assoc": null
        }]';

        $entities = $this->serializer->deserialize($json, true);

        $this->assertSame(1, count($entities));
        $this->assertSame($mainEntity, $entities[0]);
    }
}

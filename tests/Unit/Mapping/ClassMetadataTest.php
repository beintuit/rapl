<?php

namespace RAPL\Tests\Unit\Mapping;

use Doctrine\Common\Persistence\Mapping\RuntimeReflectionService;
use RAPL\RAPL\Mapping\ClassMetadata;
use RAPL\RAPL\Mapping\Route;
use RAPL\Tests\Fixtures\Entities\Book;

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    const CLASS_NAME = 'RAPL\Tests\Fixtures\Entities\Book';

    /**
     * @var ClassMetadata
     */
    private $classMetadata;

    protected function setUp()
    {
        $this->classMetadata = new ClassMetadata(self::CLASS_NAME);

        $this->classMetadata->mapField(array('fieldName' => 'id', 'type' => 'integer', 'id' => true));
        $this->classMetadata->mapField(array('fieldName' => 'title', 'serializedName' => 'book_title'));

        $reflService = new RuntimeReflectionService();

        $this->classMetadata->initializeReflection($reflService);
        $this->classMetadata->wakeupReflection($reflService);
    }

    public function testGetNameReturnsClassName()
    {
        $this->assertSame(self::CLASS_NAME, $this->classMetadata->getName());
    }

    public function testGetReflectionClassReturnsReflectionClassInstance()
    {
        $classMetadata = new ClassMetadata(self::CLASS_NAME);

        $this->assertInstanceOf('ReflectionClass', $classMetadata->getReflectionClass());
    }

    public function testCallingGetReflectionClassTwiceReturnsSameInstance()
    {
        $classMetadata = new ClassMetadata(self::CLASS_NAME);

        $this->assertSame($classMetadata->getReflectionClass(), $classMetadata->getReflectionClass());
    }

    public function testIsIdentifierReturnsTrueForIdentifierProperty()
    {
        $this->assertTrue($this->classMetadata->isIdentifier('id'));
    }

    public function testIsIdentifierReturnsFalseForNonIdentifierProperty()
    {
        $this->assertFalse($this->classMetadata->isIdentifier('title'));
    }

    public function testHasFieldReturnsTrueForMappedProperty()
    {
        $this->assertTrue($this->classMetadata->hasField('title'));
    }

    public function testHasFieldReturnsFalseForNonMappedProperty()
    {
        $this->assertFalse($this->classMetadata->hasField('foo'));
    }

    public function testGetFieldNamesReturnsMappedFieldNames()
    {
        $this->assertSame(array('id', 'title'), $this->classMetadata->getFieldNames());
    }

    public function testGetFieldNameReturnsFieldNameForSerializedName()
    {
        $this->assertSame('title', $this->classMetadata->getFieldName('book_title'));
    }

    public function testGetFieldMappingReturnsFieldMapping()
    {
        $this->assertSame(
            array('fieldName' => 'title', 'serializedName' => 'book_title', 'type' => 'string'),
            $this->classMetadata->getFieldMapping('title')
        );
    }

    public function testGetFieldMappingForNonMappedFieldThrowsException()
    {
        $this->setExpectedException('RAPL\RAPL\Mapping\MappingException');

        $this->classMetadata->getFieldMapping('fooBar');
    }

    public function testGetIdentifierFieldNames()
    {
        $this->assertEquals(array('id'), $this->classMetadata->getIdentifierFieldNames());
        $this->assertEquals(array('id'), $this->classMetadata->getIdentifier());
    }

    public function testGetTypeOfField()
    {
        $this->assertSame('integer', $this->classMetadata->getTypeOfField('id'));
        $this->assertSame('string', $this->classMetadata->getTypeOfField('title'));
    }

    public function testGetNonExistingAssociationTargetClassThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->classMetadata->getAssociationTargetClass('fooBar');
    }

    public function testIsAssociationInverseSide()
    {
        $this->classMetadata->mapField(
            array(
                'fieldName'    => 'foo',
                'association'  => ClassMetadata::EMBED_ONE,
                'isOwningSide' => false,
            )
        );

        $this->assertTrue($this->classMetadata->isAssociationInverseSide('foo'));
    }

    public function testGetAssociationMappedByTargetField()
    {
        $this->classMetadata->mapField(
            array(
                'fieldName'   => 'foo',
                'association' => ClassMetadata::EMBED_ONE,
                'mappedBy'    => 'bar',
            )
        );

        $this->assertSame('bar', $this->classMetadata->getAssociationMappedByTargetField('foo'));
    }

    public function testGetIdentifierValues()
    {
        $object = new Book();
        $object->setId(123);

        $actual = $this->classMetadata->getIdentifierValues($object);
        $this->assertSame(array('id' => 123), $actual);
    }

    public function testMapFieldValidatesAndCompletesFieldMapping()
    {
        $mapping = array(
            'fieldName' => 'test',
        );

        $this->classMetadata->mapField($mapping);

        $this->assertTrue($this->classMetadata->hasField('test'));
        $this->assertSame('string', $this->classMetadata->getTypeOfField('test'));
        $this->assertSame('test', $this->classMetadata->getSerializedName('test'));
    }

    public function testCallingMapFieldWithoutFieldNameThrowsException()
    {
        $this->setExpectedException('RAPL\RAPL\Mapping\MappingException');

        $this->classMetadata->mapField(array());
    }

    public function testEmbedOne()
    {
        $fieldName = 'author';

        $mapping = array(
            'fieldName'    => $fieldName,
            'targetEntity' => 'RAPL\Tests\Fixtures\Entities\Author',
        );

        $this->classMetadata->mapEmbedOne($mapping);

        $this->assertTrue($this->classMetadata->hasField($fieldName));
        $this->assertTrue($this->classMetadata->hasEmbed($fieldName));
        $this->assertTrue($this->classMetadata->hasAssociation($fieldName));
        $this->assertTrue($this->classMetadata->isSingleValuedAssociation($fieldName));
        $this->assertFalse($this->classMetadata->isCollectionValuedAssociation($fieldName));
        $this->assertSame(array('author'), $this->classMetadata->getAssociationNames());
        $this->assertSame(
            'RAPL\Tests\Fixtures\Entities\Author',
            $this->classMetadata->getAssociationTargetClass($fieldName)
        );
    }

    public function testCallingMapEmbedOneWithoutTargetEntityThrowsException()
    {
        $this->setExpectedException('RAPL\RAPL\Mapping\MappingException');

        $this->classMetadata->mapEmbedOne(array('fieldName' => 'author'));
    }

    public function testMapEmbedOneWithoutFieldNameThrowsException()
    {
        $this->setExpectedException('RAPL\RAPL\Mapping\MappingException');

        $this->classMetadata->mapEmbedOne(array('targetEntity' => 'RAPL\Tests\Fixtures\Entities\Author'));
    }

    public function testNewInstance()
    {
        $this->assertInstanceOf('RAPL\Tests\Fixtures\Entities\Book', $this->classMetadata->newInstance());
    }

    public function testSetGetFormat()
    {
        $this->classMetadata->setFormat('xml');

        $this->assertSame('xml', $this->classMetadata->getFormat());
    }

    public function testSetGetRoutes()
    {
        $this->assertFalse($this->classMetadata->hasRoute('resource'));
        $this->assertFalse($this->classMetadata->hasRoute('collection'));

        $this->assertNull($this->classMetadata->getRoute('resource'));

        $resourceRoute   = new Route('books/{id}', array('results', 0));
        $collectionRoute = new Route('books', array('results'));

        $this->classMetadata->setRoute('resource', $resourceRoute);
        $this->classMetadata->setRoute('collection', $collectionRoute);

        $this->assertTrue($this->classMetadata->hasRoute('resource'));
        $this->assertTrue($this->classMetadata->hasRoute('collection'));

        $this->assertSame($collectionRoute, $this->classMetadata->getRoute('collection'));
    }

    public function testSetFieldValue()
    {
        $book = new Book();

        $this->classMetadata->setFieldValue($book, 'title', 'FooBar');
        $this->assertSame('FooBar', $book->getTitle());
    }
}

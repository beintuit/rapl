<?php

namespace RAPL\Tests\Unit\Mapping\Driver;

use RAPL\RAPL\Mapping\Driver\YamlDriver;

class YamlDriverTest extends \PHPUnit_Framework_TestCase
{
    const CLASS_NAME = 'RAPL\Tests\Fixtures\Entities\Book';

    /**
     * @var YamlDriver
     */
    private $mappingDriver;

    /**
     * @var \Mockery\MockInterface|\RAPL\RAPL\Mapping\ClassMetadata
     */
    private $classMetadata;

    protected function setUp()
    {
        $paths               = array(__DIR__.'/../../../Fixtures/config/');
        $this->mappingDriver = new YamlDriver($paths);

        $this->classMetadata = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
        $this->classMetadata->shouldReceive('setFormat')->with('json')->once();
    }

    public function testLoadMetadataForClassMaps()
    {
        $this->classMetadata
            ->shouldReceive('setRoute')
            ->once()
            ->with('resource', \Mockery::type('RAPL\RAPL\Mapping\Route'));

        $this->classMetadata
            ->shouldReceive('setRoute')
            ->once()
            ->with('collection', \Mockery::type('RAPL\RAPL\Mapping\Route'));

        $this->classMetadata
            ->shouldReceive('mapField')
            ->once()
            ->with(
                array(
                    'fieldName'      => 'id',
                    'type'           => 'integer',
                    'serializedName' => null,
                    'id'             => true,
                )
            );

        $this->classMetadata
            ->shouldReceive('mapField')
            ->once()
            ->with(
                array(
                    'fieldName'      => 'title',
                    'type'           => 'string',
                    'serializedName' => null,
                )
            );

        $this->classMetadata
            ->shouldReceive('mapField')
            ->once()
            ->with(
                array(
                    'fieldName'      => 'isbn',
                    'type'           => null,
                    'serializedName' => null,
                )
            );

        $this->classMetadata
            ->shouldReceive('mapEmbedOne')
            ->once()
            ->with(
                array(
                    'targetEntity'   => 'RAPL\Tests\Fixtures\Entities\Author',
                    'fieldName'      => 'author',
                    'serializedName' => null,
                )
            );

        $this->mappingDriver->loadMetadataForClass(self::CLASS_NAME, $this->classMetadata);
    }
}

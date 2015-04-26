<?php

namespace RAPL\Tests\Unit;

use RAPL\RAPL\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp()
    {
        $this->configuration = new Configuration();
    }

    public function testGetRepositoryFactoryReturnsRepositoryFactoryInstance()
    {
        $this->assertInstanceOf('RAPL\RAPL\Repository\RepositoryFactory', $this->configuration->getRepositoryFactory());
    }

    public function testSetGetEntityNamespace()
    {
        $this->configuration->addEntityNamespace('TestNamespace', __NAMESPACE__);
        $this->assertSame(__NAMESPACE__, $this->configuration->getEntityNamespace('TestNamespace'));
    }

    public function testSetGetMappingDriver()
    {
        $mappingDriverMock = \Mockery::mock('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver');

        $this->configuration->setMetadataDriver($mappingDriverMock);

        $this->assertSame($mappingDriverMock, $this->configuration->getMetadataDriver());
    }
}

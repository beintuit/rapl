<?php

namespace RAPL\RAPL;

use Doctrine\Common\Persistence\ObjectManager;
use RAPL\RAPL\Client\HttpClient;

interface EntityManagerInterface extends ObjectManager
{
    /**
     * @return HttpClient
     */
    public function getHttpClient();

    /**
     * @return Configuration
     */
    public function getConfiguration();

    /**
     * @return UnitOfWork
     */
    public function getUnitOfWork();
}

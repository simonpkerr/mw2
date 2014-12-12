<?php

namespace Sk\MediaApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/memorywall', array(), array(), array('HTTP_ACCEPT' => 'application/json'));
               
        $this->assertTrue(strstr($client->getResponse()->getContent(), 'wallData') !== false);
        
        
    }
}

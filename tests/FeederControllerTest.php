<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FeederControllerTest extends WebTestCase
{
    public function testListPage() :void
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'feed-burner.local');
        $crawler = $client->request('GET', '/feeder/list');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();
        $this->assertContains('Add New', $client->getResponse()->getContent());
    }

    public function testNavigations() :void
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'feed-burner.local');
        $crawler = $client->request('GET', '/feeder/list');
        $link = $crawler
            ->filter('a:contains("Add New")')
            ->link();

        $crawler = $client->click($link);
        $this->assertContains('Add New / Edit Feeder', $client->getResponse()->getContent());

        //check back link

        $crawler = $client->request('GET', '/feeder/add');
        $link = $crawler
            ->filter('a:contains("Back")')
            ->link();

        $crawler = $client->click($link);
        $this->assertContains('Add New', $client->getResponse()->getContent());

    }
}

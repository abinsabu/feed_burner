<?php

namespace App\Tests;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\FeederRepository;
use App\Repository\FeedsRepository;

class HomeControllerTest extends WebTestCase
{
    public function testDashboard(): void
    {
        $client = static::createClient();
        $feederCount = self::$container->get(FeederRepository::class)->findBy(['deleted' => 0, 'hidden' => 0]);
        $feederCount = count($feederCount);
        $client->setServerParameter('HTTP_HOST', 'feed-burner.local');
        $crawler = $client->request('GET', '/dashboard');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('b', $feederCount,'Feeder count is valid -'.$feederCount);

        $feedCount = self::$container->get(FeedsRepository::class)->findBy(['deleted' => 0, 'hidden' => 0]);
        $feedCount = count($feedCount);
        $client->setServerParameter('HTTP_HOST', 'feed-burner.local');
        $this->assertResponseIsSuccessful();
        $this->assertContains((string) $feedCount, $client->getResponse()->getContent());
    }

    /**
     * @dataProvider urlProvider
     * @param string $url
     */
    public function testPageIsSuccessful(string $url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertResponseIsSuccessful(sprintf('The %s public URL loads correctly.', $url));
    }

    public function urlProvider(): ?Generator
    {
        yield ['/'];
        yield ['/dashboard'];
    }

    public function testDashboardLinks(): void
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'feed-burner.local');
        $crawler = $client->request('GET', '/dashboard');

        // Testing Sidebar menu links
        $link = $crawler
            ->filter('a:contains("Dashboard")')
            ->link();

        $crawler = $client->click($link);
        $this->assertContains('Total Feed Count', $client->getResponse()->getContent());

        $link = $crawler
            ->filter('a:contains("Feeders")')
            ->link();

        $crawler = $client->click($link);
        $this->assertContains('Title', $client->getResponse()->getContent());
        $link = $crawler
            ->filter('a:contains("Feeds")')
            ->link();

        $crawler = $client->click($link);
        $this->assertContains('Feed Description', $client->getResponse()->getContent());
    }
}

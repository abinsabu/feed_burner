<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Services;

use App\Entity\Feeder;
use App\Entity\Feeds;
use App\Repository\FeederRepository;
use App\Repository\FeedRatingRepository;
use App\Repository\FeedsRepository;
use App\Services\FeedBurner\FeedBurner;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use DOMDocument;
use LibXMLError;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use function libxml_get_errors;
use function libxml_use_internal_errors;

/**
 * BaseFeedService Class file used to manage all operations.
 *
 * @author Abin Sabu <abinsabu@gmail.com>
 */
class BaseFeedService
{

    private $feederRepository;
    private $feedsRepository;
    private $entityManger;
    private $feedRatingRepository;

    /**
     * Constructor for initialise the Class
     *
     * @param BaseFeedService $baseFeedService
     * @param FeederRepository $feederRepository
     * @param FeedsRepository $feedsRepository
     * @param FeedRatingRepository $feedRatingRepository
     */
    public function __construct(EntityManagerInterface $entityManager,
                                FeederRepository $feederRepository,
                                FeedsRepository $feedsRepository,
                                FeedRatingRepository $feedRatingRepository
    )
    {
        $this->entityManger = $entityManager;
        $this->feederRepository = $feederRepository;
        $this->feedsRepository = $feedsRepository;
        $this->feedRatingRepository = $feedRatingRepository;
    }

    /**
     * Function to fetch all the Feeders from the database with repository
     *
     * @return Feeder[]|null
     */
    public function getFeeders()
    {
        return $this->feederRepository->findBy(['deleted' => 0, 'hidden' => 0]);
    }

    /**
     * Function to fetch single Feeder from the database with repository
     *
     * @param int $feederId
     * @return Feeder[]|null
     */
    public function getSingleFeeder(int $feederId)
    {
        return $this->feederRepository->find($feederId);
    }

    /**
     * Function to save the new Feeder database with repository
     *
     * @param object $feeder
     * @return bool $status
     */
    public function saveFeeder(object $feeder)
    {
        //Perform the validation for the feeder url
        $xmlErrors = $this->doValidateFeeder($feeder);
        if (!$xmlErrors) {
            $feeder->setCreatedAt(new DateTime());
            $feeder->setUpdatedAt(new DateTime());
            $feeder->setDeleted(0);
            $feeder->setHidden(0);
            $this->entityManger->persist($feeder);
            $this->entityManger->flush();
            $status = true;
        } else {
            $status = false;
        }

        return $status;
    }

    /**
     * Function to validate the feeder URL
     *
     * @param object $feeder
     * @return LibXMLError[]
     */
    public function doValidateFeeder($feeder): bool
    {
        $data = @file_get_contents($feeder->getFeedUrl());
        $isDataError = true;
        if (!empty($data)) {
            libxml_use_internal_errors(true);
            $dummyDom = new DOMDocument('1.0', 'utf-8');
            $dummyDom->loadXML($data);
            $xmlErrorData = libxml_get_errors();
            $isDataError = false;
            if (!empty($xmlErrorData)) {
                $isDataError = true;
            }
        }

        return $isDataError;
    }

    /**
     * Function to delete the Feeder database with repository
     *
     * @param int $feederId
     * @return void
     */
    public function deleteFeeder(int $feederId): void
    {
        $feeder = $this->feederRepository->find($feederId);
        $feeder->setUpdatedAt(new DateTime());
        $feeder->setDeleted(1);
        $feeder->setHidden(1);
        $this->entityManger->persist($feeder);
        $this->entityManger->flush();
        $this->feedsRepository->deleteFeeds($feederId);
        //delete the cache data
        $this->deleteCachedData('stats.all_feeds');
    }

    /**
     * Function delete cached data based on the key.
     * @return
     */

    private function deleteCachedData($cacheKey): bool
    {
        $cacheManager = new FilesystemAdapter();
        // retrieve the cache item
        $cachedItem = $cacheManager->getItem($cacheKey);
        if ($cachedItem->isHit()) {
            // remove the cache item
            $cacheManager->deleteItem($cacheKey);
        }

        return true;
    }

    /**
     * Function to read the Feeder link and add/update data to the database with repository
     *
     * @param int $feederId
     * @return int[]
     */
    public function readFeeder(int $feederId): array
    {
        $newFeedCount = 0;
        $updatedFeedCount = 0;
        $feedCount = 10000;
        $feederObj = $this->feederRepository->find($feederId);

        //Perform the validation for the feeder url
        $xmlErrors = $this->doValidateFeeder($feederObj);

        if ($xmlErrors) {
            return false;
        }
        // Create and instance and read the feed URL
        $feeds = new FeedBurner($feederObj->getFeedUrl());
        if ($feeds->count() > 0) {
            //delete the cache data
            $this->deleteCachedData('stats.all_feeds');
            // Iterate through each node and insert to database
            foreach ($feeds->find($feedCount) as $key => $value) {
                // Check if the feed already available or not in the database
                $isFeedNew = $this->doCheckFeedDuplication($feederId, $value);
                if (empty($isFeedNew)) {
                    $feedObj = new Feeds();
                    // If feed is not available, then insert the data as new entry.
                    $this->saveFeedData($feederObj, $feedObj, $value);
                    $newFeedCount++;
                } else {
                    // If feed is available, then update the data as new values.
                    $this->saveFeedData($feederObj, $isFeedNew, $value);
                    $updatedFeedCount++;
                }
            }
        }

        return ['new_feed_count' => $newFeedCount, 'skipped_feed_count' => $updatedFeedCount];
    }

    /**
     * Function to check the available of feed on the database.
     *
     * @param int $feederId
     * @param obj $feed
     * @return Feeds|null
     */
    public function doCheckFeedDuplication(int $feederId, $feed)
    {
        $checkConditions = ['feeder' => $feederId, 'unique_id' => $feed->unique_id];
        return $this->feedsRepository->findOneBy($checkConditions);
    }

    /**
     * Function to save feed data to the database.
     *
     * @param obj $feederObj
     * @param obj $feedObj
     * @param obj $feedData
     * @return void
     */
    public function saveFeedData($feederObj, $feedObj, $feedData): void
    {
        $feedObj->setTitle($feedData->title);
        $feedObj->setDescription($feedData->description);
        $feedObj->setFeeder($feederObj);
        $feedObj->setFeedData($feedData->json);
        $feedObj->setFeedUrl($feedData->link);
        $feedObj->setImageUrl($feedData->image);
        $feedObj->setUniqueId($feedData->unique_id);
        $feedObj->setDeleted(0);
        $feedObj->setHidden(0);
        $this->entityManger->persist($feedObj);
        $this->entityManger->flush();
    }

    /**
     * Function to get single feed data.
     *
     * @param int $feedId
     * @return
     */
    public function getSingleFeed(int $feedId)
    {
        return $this->feedsRepository->findOneById($feedId);
    }

    /**
     * Function to save the feed-rating for each feed.
     *
     * @param object $feedRating
     * @param int $feedId
     * @return void
     */
    public function saveFeedRating(object $feedRating, int $feedId): void
    {
        $feed = $this->feedsRepository->findOneById($feedId);
        $feedRating->setIpAddress($_SERVER['REMOTE_ADDR']);
        $feedRating->setFeed($feed);
        $this->entityManger->persist($feedRating);
        $this->entityManger->flush();
    }

    /**
     * Function to check the rating given to the each feed
     *
     * @param object $feedRating
     * @param int $feedId
     * @return
     */
    public function checkFeedRating(int $feedId)
    {
        $userIp = $_SERVER['REMOTE_ADDR'];
        return $this->feedRatingRepository->findOneBy(['ip_address' => $userIp, 'feed' => $feedId]);
    }

    /**
     * Function get all feed data for the dashboard.
     *
     * @return array
     */
    public function getAllFeedData(): array
    {
        $feedData = $this->feedsRepository->findBy(['deleted' => 0, 'hidden' => 0]);
        $feederData = $this->feederRepository->findBy(['deleted' => 0, 'hidden' => 0]);
        $feedCount = count($feedData);
        $feederCount = count($feederData);
        return ['feedCount' => $feedCount, 'feederCount' => $feederCount];
    }

    /**
     * Function get all feeds from the database.
     * @return Feeds[]
     */
    public function getAllFeeds(): array
    {
        $allFeedData = $this->fetchCachedData('stats.all_feeds');
        if (empty($allFeedData)) {
            $allFeedData = $this->feedsRepository->findBy(['deleted' => 0, 'hidden' => 0]);
            $this->saveCachedData('stats.all_feeds', $allFeedData);
        }

        return $allFeedData;
    }

    /**
     * Function get all cached data based on the key.
     *
     * @param $cacheKey
     * @return array
     */

    private function fetchCachedData($cacheKey): array
    {
        $cachedData = [];
        $cacheManager = new FilesystemAdapter();
        // retrieve the cache item
        $cachedItem = $cacheManager->getItem($cacheKey);
        if ($cachedItem->isHit()) {
            $cachedData = $cachedItem->get();
        }

        return $cachedData;
    }

    /**
     * Function save cached data based on the key.
     *
     * @param $cacheData
     * @param $cacheKey
     * @return bool
     */

    private function saveCachedData($cacheKey, $cacheData): bool
    {
        $cacheManager = new FilesystemAdapter();
        // retrieve the cache item
        $cachedItem = $cacheManager->getItem($cacheKey);
        if ($cachedItem->isHit()) {
            $cacheManager->deleteItem($cacheKey);
        }
        // assign a value to the item and save it
        $cachedItem->set($cacheData);
        $cacheManager->save($cachedItem);

        return true;
    }

    /**
     * Function check the feeder already exists
     *
     * @param $feeder
     * @return bool
     */
    public function checkFeederExists($feeder)
    {
        $checkAvailabiliy = $this->feederRepository->findBy([
            'feed_url' => $feeder->getFeedUrl(),
            'deleted' => 0,
            'hidden' => 0
        ]);

        return (count($checkAvailabiliy) > 0) ? true : false;
    }
}
<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\FeedRating;
use App\Form\RatingFormType;
use App\Services\BaseFeedService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage Feeds in the feedburner application.
 *
 * @Route("/feed")
 *
 * @author Abin Sabu <abinsabu@gmail.com>
 */
class FeedsController extends AbstractController
{

    private $baseFeedService;

    /**
     * Constructor for initialise the Class
     *
     * @param BaseFeedService $baseFeedService
     */

    public function __construct(BaseFeedService $baseFeedService)
    {
        $this->baseFeedService = $baseFeedService;
    }

    /**
     * List all Feeds for the single feeder added to the application.
     *
     * @Route("/feed/list/{id}", name="feeds_list")
     *
     * @param $id
     * @return Response
     */
    public function list($id): Response
    {
        $feeds = $this->baseFeedService->getSingleFeeder($id);
        return $this->render('feeds/feeds_list.html.twig', [
            'feeds' => ($feeds) ? $feeds->getFeeds() : null,
        ]);
    }

    /**
     * List all active Feeds from all the Feeders added to the application.
     *
     * @Route("/feed/list-all/", name="feeds_list_all")
     *
     * @return Response
     */
    public function listAll(): Response
    {
        $feeds = $this->baseFeedService->getAllFeeds();
        return $this->render('feeds/feeds_list.html.twig', [
            'feeds' => $feeds,
        ]);
    }

    /**
     * Detail view for the Feed on the application.
     *
     * @Route("/feed/view/{id}", name="feed_view")
     *
     * @param $id
     * @return Response
     */
    public function detail($id): Response
    {
        $feedRatingValue = null;
        $feedData = '';
        $feederId = 1;
        $feed = $this->baseFeedService->getSingleFeed($id);
        // Get the rating for the current feed from the user
        $feedRating = $this->baseFeedService->checkFeedRating($id);
        if (!empty($feedRating)) {
            $feedRatingValue = $feedRating->getRating();
        }
        //Decompose the feed data for the detail page.
        if ($feed) {
            $feedData = json_decode($feed->getFeedData(), true);
            $feederId = $feed->getFeeder()->getId();
        }

        return $this->render('feeds/feed_detail.html.twig', [
            'viewFeedData' => $feedData,
            'feed_id' => $id,
            'feed_rating' => $feedRatingValue,
            'feeder_id' => $feederId,
        ]);
    }

    /**
     * Add rating to the Feed on the application.
     *
     * @Route("/feed/add-rating/{id}", name="feed_add_rating")
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function addRating(Request $request, $id): Response
    {
        $status = false;
        $feedRating = new FeedRating();
        $ratingForm = $this->createForm(RatingFormType::class, $feedRating);
        $ratingForm->handleRequest($request);
        if ($ratingForm->isSubmitted() && $ratingForm->isValid()) {
            $this->baseFeedService->saveFeedRating($feedRating, $id);
            $status = true;
        }

        return $this->render('feeds/add_feed_rating.html.twig', [
            'is_saved' => $status,
            'feed_id' => $id,
            'ratingForm' => $ratingForm->createView(),
        ]);
    }
}

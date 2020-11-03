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

use App\Services\BaseFeedService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage Dashboard in the feedburner application.
 *
 * @Route("/dashboard")
 *
 * @author Abin Sabu <abinsabu@gmail.com>
 */
class HomeController extends AbstractController
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
     * Display the dashboard details for this application
     *
     * @Route("/dashboard", name="dashboard")
     *
     * @return Response
     */
    public function dashboard(): Response
    {
        $feedData = $this->baseFeedService->getAllFeedData();
        return $this->render('home/dashboard.html.twig', [
            'feedData' => $feedData,
        ]);
    }
}

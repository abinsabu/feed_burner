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

use App\Entity\Feeder;
use App\Form\FeederFormType;
use App\Services\BaseFeedService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller used to manage Feeders in the feedburner application.
 *
 * @Route("/feeder")
 *
 * @author Abin Sabu <abinsabu@gmail.com>
 */
class FeederController extends AbstractController
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
     * List all Feeders added to the application.
     *
     * @Route("/feeder/list", name="feeder_list")
     *
     * @return Response
     */
    public function list(): Response
    {
        $feeders = $this->baseFeedService->getFeeders();
        return $this->render('feeder/feeder_list.html.twig', [
            'feeders' => $feeders,
        ]);
    }

    /**
     * Adds new Feeder to the application
     *
     * @Route("/feeder/add", name="add_new_feeder")
     *
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        $status = false;
        $isValidFeeder = true;
        $feeder = new Feeder();
        $feederForm = $this->createForm(FeederFormType::class, $feeder);
        $feederForm->handleRequest($request);
        if ($feederForm->isSubmitted() && $feederForm->isValid()) {
            $isValidFeeder = false;
            if (!$this->baseFeedService->checkFeederExists($feeder)) {
                if ($this->baseFeedService->saveFeeder($feeder)) {
                    $status = true;
                    $isValidFeeder = true;
                }
            }
        }

        return $this->render('feeder/add_new_form.html.twig', [
            'feeder_form' => $feederForm->createView(),
            'is_saved' => $status,
            'is_valid_feeder' => $isValidFeeder
        ]);
    }

    /**
     * Edit a Feeder application
     *
     * @Route("/feeder/edit/{id}", name="feeder_edit")
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function edit(Request $request, $id): Response
    {
        $status = false;
        $isValidFeeder = true;
        $feederData = $this->baseFeedService->getSingleFeeder($id);
        $feederForm = $this->createForm(FeederFormType::class, $feederData);
        $feederForm->handleRequest($request);
        if ($feederForm->isSubmitted() && $feederForm->isValid()) {
            $isValidFeeder = false;
            if ($this->baseFeedService->saveFeeder($feederData)) {
                $status = true;
                $isValidFeeder = true;
            }
        }

        return $this->render('feeder/edit_form.html.twig', [
            'feeder_form' => $feederForm->createView(),
            'is_saved' => $status,
            'is_valid_feeder' => $isValidFeeder
        ]);
    }

    /**
     * Delete the feeder and associated feeds from the application
     *
     * @Route("/feeder/delete/{id}", name="feeder_delete")
     *
     * @param $id
     * @return Response
     */
    public function delete($id): Response
    {
        $this->baseFeedService->deleteFeeder($id);
        return $this->render('feeder/delete_feeder.html.twig');
    }

    /**
     * Execute and grab the feeds from the Feeder URL
     *
     * @Route("/feeder/execute/{id}", name="feeder_exe")
     *
     * @param $id
     * @return Response
     */
    public function burn($id): Response
    {
        $response = $this->baseFeedService->readFeeder($id);
        return $this->render('feeder/execute_feeder.html.twig', ['result' => $response]);
    }
}

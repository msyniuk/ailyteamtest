<?php

namespace App\Controller\Rest;


use App\Service\LinkService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LinkController extends AbstractFOSRestController
{
    /**
     * @var LinkService
     */
    private $linkService;
    
    /**
     * LinkController constructor.
     * @param LinkService $linkService
     */
    public function __construct(LinkService $linkService)
    {
        $this->linkService = $linkService;
    }

    /**
     * Creates an link resource
     * @Rest\Post("/links")
     */
    public function createLink(Request $request): View
    {
        $link = $this->linkService->addLink($request->get('url'), $request->get('lifetime'));

        // 201 HTTP CREATED response with the created object
        return View::create($link, Response::HTTP_CREATED);
    }

    /**
     * Retrieves an link resource
     * @Rest\Get("/links/{linkId}")
     */
    public function getLink(int $linkId): View
    {
        $link = $this->linkService->getLink($linkId);

        // 200 HTTP OK response with the request object
        return View::create($link, Response::HTTP_OK);
    }

    /**
     * Retrieves a collection of link resource
     * @Rest\Get("/links")
     */
    public function getLinks(): View
    {
        $links = $this->linkService->getAllLinks();

        // 200 HTTP OK response with the collection of link object
        return View::create($links, Response::HTTP_OK);
    }

    /**
     * Replaces link resource
     * @Rest\Put("/links/{linkId}")
     */
    public function putLink(int $linkId, Request $request): View
    {
        $link = $this->linkService->updateLink($linkId, $request->get('url'),
            $request->get('lifetime'), $request->get('shorturl'), $request->get('active'));

        // 200 HTTP OK response with the object as a result of PUT
        return View::create($link, Response::HTTP_OK);
    }

    /**
     * Removes the link resource
     * @Rest\Delete("/links/{linkId}")
     */
    public function deleteLink(int $linkId): View
    {
        $this->linkService->deletelink($linkId);

        // 204 HTTP NO CONTENT response. The object is deleted.
        return View::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Retrieves an link resource
     * @Rest\Get("/links/statistics/{linkId}")
     */
    public function getLinkStatistics(int $linkId): View
    {
        $statistics = $this->linkService->getLinkStatistics($linkId);

        // 200 HTTP OK response with the request object
        return View::create($statistics, Response::HTTP_OK);
    }

}
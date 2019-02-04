<?php

namespace App\Controller;


use App\Entity\Link;
use App\Service\LinkService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class WebController extends AbstractController
{
    /**
     * @var LinkService
     */
    private $linkService;

    /**
     * @param LinkService $linkService
     */
    public function __construct(LinkService $linkService)
    {
        $this->linkService = $linkService;
    }


    /**
     * @Route("/{hash}", name="link")
     */
    public function goLink($hash)
    {
        /**
         * @var Link $link
         */
        $link = $this->linkService->getLinkByHash($hash);

        if (!$link) {
            throw $this->createNotFoundException('Link not found');
        }

        if (!$link->getIsActive()) {
            throw $this->createNotFoundException('Link not active');
        }

        $now = new \DateTime(); // now date
        $date = $link->getCreateAt();
        $interval = $now->diff($date);
        $days = $interval->d;

        if ($days > $link->getLifeTime()) {
            throw $this->createNotFoundException('Life time experied');
        }

        $this->linkService->addLinkStatistic($link);

        return $this->redirect($link->getUrl());
    }

}

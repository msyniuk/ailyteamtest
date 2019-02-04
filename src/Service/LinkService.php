<?php

namespace App\Service;

use App\Entity\Link;
use App\Entity\Statistic;
use App\Repository\LinkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

class LinkService
{
    /**
     * @var LinkRepository
     */
    private $linkRepository;

    public function __construct(LinkRepository $linkRepository){

        $this->linkRepository = $linkRepository;

    }

    public function getLink(int $linkId): ?Link
    {
        return $this->linkRepository->findById($linkId);
    }

    public function getAllLinks(): ?array
    {
        return $this->linkRepository->findAll();
    }

    public function addLink(string $url, ?int $lifeTime): Link
    {
        $link = new Link();

        if (substr($url, -1) == '/') {
            $link->setUrl($url);
        } else {
            $link->setUrl($url . '/');
        }

        $link->setCreateAt(new \DateTime());
        $link->setIsActive(true);

        if (!$lifeTime) {
            $link->setLifeTime(1);
        } else {
            $link->setLifeTime($lifeTime);
        }


        $link->setShortUrl(bin2hex(random_bytes(3)));
        $this->linkRepository->save($link);

        return $link;
    }

    public function updateLink(int $linkId, Request $request): ?Link
    {
        $link = $this->linkRepository->findById($linkId);

        if (!$link) {
            return null;
        }

        $url = $request->get('url');

        if ($url) {
            $link->setUrl($url);
        }

        $date = $request->get('date');

        if ($date) {
            $link->setCreateAt(new \DateTime($date));
        }

        $lifeTime = $request->get('lifetime');

        if ($lifeTime) {
            $link->setLifeTime($lifeTime);
        }

        $active = $request->get('active');

        if (!$active === null) {

            if ($active) {
                $link->setIsActive(true);
            } else {
                $link->setIsActive(false);
            }

        }

        $shortUrl = $request->get('shortUrl');

        if ($shortUrl) {
            $link->setShortUrl($shortUrl);
        }

        $this->linkRepository->save($link);

        return $link;
    }

    public function deleteLink(int $linkId): void
    {
        $link = $this->linkRepository->findById($linkId);

        if ($link) {
            $this->linkRepository->delete($link);
        }
    }

    public function getLinkStatistics(int $linkId): ?array
    {
        $statisctics = $this->linkRepository->findStatisticsById($linkId);

        return $statisctics;
    }

    public function getLinkByHash(string $hash): ?Link
    {
        return $this->linkRepository->findByHash($hash);
    }

    public function addLinkStatistic($link): void
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $referer = $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];

        $linkStatistic = new Statistic();

        $linkStatistic->setDate(new \DateTime());
        $linkStatistic->setLink($link);
        $linkStatistic->setBrowser($browser);
        $linkStatistic->setIpAddress($ip);
        $linkStatistic->setReferer($referer);

        $this->linkRepository->saveStatistic($linkStatistic);
    }

}

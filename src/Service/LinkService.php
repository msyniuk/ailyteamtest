<?php

namespace App\Service;

use App\Entity\Link;
use App\Repository\LinkRepository;
use Doctrine\Common\Collections\ArrayCollection;

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

    public function addLink(string $url, int $lifeTime): Link
    {
        $link = new Link();
        $link->setUrl($url);
        $link->setCreateAt(new \DateTime());
        $link->setIsActive(true);
        $link->setLifeTime($lifeTime);
        $link->setShortUrl('http://loca.ly/' . random_bytes(5));
        $this->linkRepository->save($link);

        return $link;
    }

    public function updateLink(int $linkId, string $url, int $lifeTime, string $shortUrl, int $active): ?Link
    {
        $link = $this->linkRepository->findById($linkId);

        if (!$link) {
            return null;
        }

        $link->setUrl($url);
        $link->setCreateAt(new \DateTime());

        if ($active) {
            $link->setIsActive(true);
        } else {
            $link->setIsActive(false);
        }

        $link->setLifeTime($lifeTime);
        $link->setShortUrl($shortUrl . random_bytes(5));
        $this->linkRepository->update($link);

        return $link;
    }

    public function deleteLink(int $linkId): void
    {
        $link = $this->linkRepository->findById($linkId);

        if ($link) {
            $this->linkRepository->delete($link);
        }
    }

    public function getLinkStatistics(int $linkId): ?ArrayCollection
    {
        $statisctics = $this->linkRepository->findStatisticsById($linkId);

        return $statisctics;
    }

}

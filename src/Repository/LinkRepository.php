<?php

namespace App\Repository;

use App\Entity\Link;
use App\Entity\Statistic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Link|null find($id, $lockMode = null, $lockVersion = null)
 * @method Link|null findOneBy(array $criteria, array $orderBy = null)
 * @method Link[]    findAll()
 * @method Link[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var ObjectRepository
     */
    private $objectRepository;


    public function __construct(RegistryInterface $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Link::class);

        $this->em = $em;

        $this->objectRepository = $this->em->getRepository(Link::class);

    }

    public function findById($id)
    {
        $result = $this->objectRepository->find($id);

        return $result;
    }

    /**
     * @var Link $link
     */
    public function save($link)
    {
        if (!$link->getId()) {
            $this->em->persist($link);
        }

        $this->em->flush();
    }

    public function delete($link)
    {
        $this->em->remove($link);
        $this->em->flush();
    }

    public function findStatisticsById($id)
    {
        $qbAll = $this->em->createQueryBuilder();
        $resultAll = $qbAll->select('IDENTITY(s.link) as linkId')
            ->from(Statistic::class, 's')
            ->addSelect('COUNT(IDENTITY(s.link)) AS visits')
            ->join('s.link', 'l')
            ->andWhere('l.id = :id')
            ->andWhere('s.link IN (:ids)')
            ->setParameter('id', $id)
            ->setParameter('ids', [$id])
            ->groupBy('s.link')
            ->getQuery()
            ->getResult(Query::HYDRATE_SCALAR)
        ;

        $qbRef = $this->em->createQueryBuilder();
        $resultRef = $qbRef->select('IDENTITY(s.link) as linkId')
            ->from(Statistic::class, 's')
            ->addSelect('s.referer, COUNT(s.referer) AS visits_by_referer')
            ->join('s.link', 'l')
            ->andWhere('l.id = :id')
            ->setParameter('id', $id)
            ->groupBy('s.link')
            ->addGroupBy('s.referer')
            ->getQuery()
            ->getResult(Query::HYDRATE_SCALAR)
        ;

        $qbBrowser = $this->em->createQueryBuilder();
        $resultBrowser = $qbBrowser->select('IDENTITY(s.link) as linkId')
            ->from(Statistic::class, 's')
            ->addSelect('s.browser, COUNT(s.browser) AS visits_by_browser')
            ->join('s.link', 'l')
            ->andWhere('l.id = :id')
            ->setParameter('id', $id)
            ->groupBy('s.link')
            ->addGroupBy('s.browser')
            ->getQuery()
            ->getResult(Query::HYDRATE_SCALAR)
        ;

        $qbDate = $this->em->createQueryBuilder();
        $resultDate = $qbDate->select('IDENTITY(s.link) as linkId')
            ->from(Statistic::class, 's')
            ->addSelect('DATE(s.date) as date, COUNT(DATE(s.date)) AS visits_by_date')
            ->join('s.link', 'l')
            ->andWhere('l.id = :id')
            ->setParameter('id', $id)
            ->groupBy('s.link')
            ->addGroupBy('date')
            ->getQuery()
            ->getResult(Query::HYDRATE_SCALAR)
        ;

        foreach ($resultRef as $row) {
            $resultAll[0]['referer'][$row['referer']] = $row['visits_by_referer'];
        }

        foreach ($resultBrowser as $row) {
            $resultAll[0]['browser'][$row['browser']] = $row['visits_by_browser'];
        }

        foreach ($resultDate as $row) {
            $resultAll[0]['date'][$row['date']] = $row['visits_by_date'];
        }

        return $resultAll;

    }

    public function findByHash($hash)
    {
        $result = $this->objectRepository->findOneBy(['shortUrl' => $hash]);

        return $result;
    }

    /**
     * @var Statistic $linkStatistic
     */
    public function saveStatistic($linkStatistic)
    {
        $this->em->persist($linkStatistic);
        $this->em->flush();
    }

}

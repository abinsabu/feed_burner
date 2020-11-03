<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Feeds;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Feeds|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feeds|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feeds[]    findAll()
 * @method Feeds[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feeds::class);
    }

    /**
     * Set deleted = 1  the feeds after the Feeder is deleted
     *
     * @param $feederId
     */
    public function deleteFeeds($feederId)
    {
        $builder = $this->createQueryBuilder('feeds');
        $builder->update()
            ->set('feeds.deleted', 1)
            ->where('feeds.feeder = ?1')
            ->setParameter(1, $feederId)
            ->getQuery()
            ->execute();
    }

}

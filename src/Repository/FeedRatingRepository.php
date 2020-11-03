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

use App\Entity\FeedRating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FeedRating|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeedRating|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeedRating[]    findAll()
 * @method FeedRating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedRatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedRating::class);
    }
}

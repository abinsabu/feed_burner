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

use App\Entity\Feeder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Feeder|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feeder|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feeder[]    findAll()
 * @method Feeder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeederRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feeder::class);
    }
}

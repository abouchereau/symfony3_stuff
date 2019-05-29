<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PointRepository extends EntityRepository
{
    public function findAllArray()
    {
        return $this->createQueryBuilder('p')
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);
    }
}
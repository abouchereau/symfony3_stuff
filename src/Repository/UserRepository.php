<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserRepository extends EntityRepository
{
    public function findAllArray()
    {
        return $this->createQueryBuilder('u')
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);
    }
}
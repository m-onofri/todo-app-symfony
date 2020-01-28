<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    /**
     * @return Activity[] Returns an array of Activity objects
     */
    public function findActivitiesByStatus($status, $project_id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.status = :val')
            ->andWhere('a.projectId = :pid')
            ->setParameter('val', $status)
            ->setParameter('pid', $project_id)
            ->orderBy('a.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function deleteActivitiesByProjectId($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            DELETE FROM activity
            WHERE project_id = :id
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    /*
    public function findOneBySomeField($value): ?Activity
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

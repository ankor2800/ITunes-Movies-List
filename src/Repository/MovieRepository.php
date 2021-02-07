<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class MovieRepository extends EntityRepository
{
    public function findLatest(int $count): ArrayCollection
    {
        $data = parent::findBy([], ['id' => Criteria::DESC], $count);

        return new ArrayCollection($data);
    }

    public function find($id, $lockMode = null, $lockVersion = null): Movie
    {
        $movie = parent::find($id, $lockMode, $lockVersion);

        if ($movie === null) {
            throw new NotFoundException(sprintf('Movie id: %d not found', $id));
        }

        return $movie;
    }
}

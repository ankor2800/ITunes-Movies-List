<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\ORM\EntityRepository;

class MovieRepository extends EntityRepository
{
    public function find($id, $lockMode = null, $lockVersion = null): Movie
    {
        $movie = $this->_em->find($this->_entityName, $id, $lockMode, $lockVersion);

        if ($movie === null) {
            throw new NotFoundException(sprintf('Movie id: %d not found', $id));
        }

        return $movie;
    }
}

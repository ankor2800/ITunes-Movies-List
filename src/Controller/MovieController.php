<?php declare(strict_types=1);

namespace App\Controller;

use function Amp\Promise\rethrow;
use App\Container\ServiceNotFoundException;
use App\Entity\Movie;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorInterface;
use Twig\Environment;

class MovieController
{
    public function __construct(
        private Environment $twig,
        private EntityManagerInterface $em
    ) {}

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $data = $this->twig->render('movie.html.twig', [
                'movie'    => $this->getData((int) $request->getAttribute('movie_id')),
            ]);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        $response->getBody()->write($data);

        return $response;
    }

    protected function getData(int $id): Movie
    {
        return $this->em->getRepository(Movie::class)
            ->find($id);
    }
}

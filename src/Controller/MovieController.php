<?php declare(strict_types=1);

namespace App\Controller;

use function Amp\Promise\rethrow;
use App\Entity\Movie;
use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\NotFoundException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Exception;

class MovieController
{
    public function __construct(
        private Environment $twig,
        private EntityManagerInterface $em
    ) {}

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $movie_id = (int) $request->getAttribute('movie_id');
        if (empty($movie_id)) {
            throw new HttpBadRequestException($request);
        }

        try {
            $data = $this->twig->render('movie.html.twig', [
                'movie' => $this->getData($movie_id),
            ]);
        } catch (NotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage(), $e);
        } catch (Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        $response->getBody()->write($data);

        return $response;
    }

    protected function getData(int $id): Movie
    {
        return $this->em->getRepository(Movie::class)->find($id);
    }
}

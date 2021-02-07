<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Movie;
use Twig\Environment;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Exception;

class HomeController
{
    public function __construct(
        private Environment $twig,
        private EntityManagerInterface $em
    ) {}

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $data = $this->twig->render('home/index.html.twig', [
                'trailers' => $this->fetchData(),
            ]);
        } catch (Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        $response->getBody()->write($data);

        return $response;
    }

    protected function fetchData(): ArrayCollection
    {
        return $this->em->getRepository(Movie::class)->findLatest(10);
    }
}

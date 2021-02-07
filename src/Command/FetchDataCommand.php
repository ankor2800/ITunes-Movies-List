<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Movie;
use App\Service\Provider\ItunesProvider;
use App\Service\Trailer;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Exception;

class FetchDataCommand extends Command
{
    protected static $defaultName = 'fetch:trailers';
    protected static $defaultDescription = 'Fetch movie trailers';

    private ClientInterface $httpClient;
    private LoggerInterface $logger;
    private string $source;
    private EntityManagerInterface $doctrine;

    /**
     * FetchDataCommand constructor.
     *
     * @param ClientInterface        $httpClient
     * @param LoggerInterface        $logger
     * @param EntityManagerInterface $em
     * @param string|null            $name
     */
    public function __construct(ClientInterface $httpClient, LoggerInterface $logger, EntityManagerInterface $em, string $name = null)
    {
        parent::__construct($name);
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->doctrine = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $iTunesProvider = new ItunesProvider();

        $trailer = new Trailer($iTunesProvider);

        $io = new SymfonyStyle($input, $output);
        $io->title(sprintf('Fetch data from %s', $trailer->getSourceTitle()));

        $this->logger->info(sprintf('Start %s at %s', __CLASS__, (string) date_create()->format(DATE_ATOM)));

        try {
            $trailers = $trailer->getTrailers();

            foreach ($trailers as $trailer) {
                $movie = $this->getMovie($trailer->getTitle())
                    ->setTitle($trailer->getTitle())
                    ->setDescription($trailer->getDescription())
                    ->setLink($trailer->getLink())
                    ->setImage($trailer->getImage())
                    ->setPubDate($trailer->getPubDate());

                $this->doctrine->persist($movie);
            }

            $this->doctrine->flush();

        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        $this->logger->info(sprintf('End %s at %s', __CLASS__, (string) date_create()->format(DATE_ATOM)));

        return 0;
    }

    protected function getMovie(string $title): Movie
    {
        $item = $this->doctrine->getRepository(Movie::class)->findOneBy(['title' => $title]);

        if ($item === null) {
            $this->logger->info('Create new Movie', ['title' => $title]);
            $item = new Movie();
        } else {
            $this->logger->info('Move found', ['title' => $title]);
        }

        if (!($item instanceof Movie)) {
            throw new RuntimeException('Wrong type!');
        }

        return $item;
    }
}

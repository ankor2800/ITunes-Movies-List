<?php

namespace App\Service\Provider;

use App\Model\Trailer;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;

class ItunesProvider implements TrailerProviderInterface
{
    private const SOURCE = 'https://trailers.apple.com/trailers/home/rss/newtrailers.rss';

    private Client $httpClient;

    private string $sourceTitle = 'iTunes Movie Trailers';

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    public function getSourceTitle(): string
    {
        return $this->sourceTitle;
    }

    public function getTrailers(): ArrayCollection
    {
        $data = $this->execute();

        $trailers = $this->processXml($data);

        return $trailers;
    }

    private function execute(): string
    {
        try {
            $response = $this->httpClient->sendRequest(
                new Request('GET', self::SOURCE)
            );
        } catch (ClientExceptionInterface $e) {
            throw new \Exception($e->getMessage());
        }

        if (($status = $response->getStatusCode()) !== 200) {
            throw new \Exception(sprintf('Response status is %d, expected %d', $status, 200));
        }

        return $response->getBody()->getContents();
    }

    private function processXml($data): ArrayCollection
    {
        $xml = (new \SimpleXMLElement($data))->children();

        if (!property_exists($xml, 'channel')) {
            throw new \Exception('Could not find \'channel\' element in feed');
        }

        $trailers = new ArrayCollection();

        foreach ($xml->channel->item as $item) {
            $title = $this->getTitle($item);
            $description = $this->getDescription($item);
            $link = $this->getLink($item);
            $image = $this->getImage($item);
            $pubDate = $this->getPubDate($item);

            $trailer = (new Trailer())
                ->setTitle($title)
                ->setDescription($description)
                ->setLink($link)
                ->setImage($image)
                ->setPubDate($pubDate);

            $trailers->add($trailer);
        }

        return $trailers;
    }

    private function getTitle(\SimpleXMLElement $item): string
    {
        return $item->title;
    }

    private function getDescription(\SimpleXMLElement $item): string
    {
        return $item->description;
    }

    private function getLink(\SimpleXMLElement $item): string
    {
        return $item->link;
    }

    private function getImage(\SimpleXMLElement $item): string
    {
        return $item->link . '/images/poster.jpg';
    }

    private function getPubDate(\SimpleXMLElement $item): \DateTime
    {
        return new \DateTime($item->pubDate);
    }
}

<?php

namespace App\Service;

use App\Service\Provider\TrailerProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class Trailer
{
    private const LIMIT = 10;

    private TrailerProviderInterface $provider;

    public function __construct(TrailerProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function getSourceTitle(): string
    {
        return $this->provider->getSourceTitle();
    }

    public function getTrailers(): ArrayCollection
    {
        $trailers = $this->provider->getTrailers();

        $criteriaDate = Criteria::create()
            ->orderBy(["pubDate" => Criteria::DESC]);

        $sortedTrailers = $trailers->matching($criteriaDate);

        $data = $sortedTrailers->slice(0, self::LIMIT);

        return new ArrayCollection($data);
    }
}

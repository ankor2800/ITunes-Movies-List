<?php

namespace App\Service;

use App\Service\Provider\TrailerProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;

class Trailer
{
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

        return $trailers;
    }
}

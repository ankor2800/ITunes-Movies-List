<?php

namespace App\Service\Provider;

use Doctrine\Common\Collections\ArrayCollection;

interface TrailerProviderInterface
{
    public function getSourceTitle(): string;

    public function getTrailers(): ArrayCollection;
}

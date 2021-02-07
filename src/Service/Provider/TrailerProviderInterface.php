<?php

namespace App\Service\Provider;

use Doctrine\Common\Collections\ArrayCollection;

interface TrailerProviderInterface
{
    function getSourceTitle(): string;

    function getTrailers(): ArrayCollection;
}

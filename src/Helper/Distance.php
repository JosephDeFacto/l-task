<?php

namespace App\Helper;

class Distance
{
    /**
     * @var array|string[]
     */
    private array $distances = [ "medium", "long"];

    /**
     * @return array
     */
    public function getDistance(): ?array
    {
        return !$this->distances ? [] : $this->distances;
    }
}
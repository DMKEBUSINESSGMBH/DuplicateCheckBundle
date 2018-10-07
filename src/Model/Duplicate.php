<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Model;

class Duplicate implements DuplicateInterface
{
    protected $object;

    protected $weight;

    public function __construct($object, float $weight = 0.5)
    {
        $this->object = $object;
        $this->weight = $weight;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getClass(): string
    {
        return get_class($this->object);
    }

}
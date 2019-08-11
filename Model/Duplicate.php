<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Model;

class Duplicate implements DuplicateInterface
{
    /**
     * @var object
     */
    protected $object;

    /**
     * @var float
     */
    protected $weight;

    /**
     * Duplicate constructor.
     *
     * @param object $object
     * @param float  $weight
     */
    public function __construct($object, float $weight = 0.5)
    {
        $this->object = $object;
        $this->weight = $weight;
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * {@inheritdoc}
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass(): string
    {
        return get_class($this->object);
    }
}

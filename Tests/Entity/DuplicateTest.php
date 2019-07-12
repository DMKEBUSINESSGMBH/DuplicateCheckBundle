<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Tests\Entity;

use DMK\DuplicateCheckBundle\Entity\Duplicate;
use PHPUnit\Framework\TestCase;

class DuplicateTest extends TestCase
{
    protected function setup()
    {
        $this->original = new \stdClass();
        $this->object = new Duplicate($this->original, 1, 0.8);
    }

    public function test_class()
    {
        $this->assertEquals('stdClass', $this->object->getClass());
    }

    public function test_id()
    {
        $this->assertEquals(1, $this->object->getId());
    }

    public function test_object()
    {
        $this->assertEquals($this->original, $this->object->getObject());
    }

    public function test_weight()
    {
        $this->assertEquals(0.8, $this->object->getWeight());
    }
}

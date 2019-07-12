<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Tests;

use DMK\DuplicateCheckBundle\Facade;
use DMK\DuplicateCheckBundle\FinderInterface;
use DMK\DuplicateCheckBundle\Model\DuplicateInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class FacadeTest extends TestCase
{
    protected function setup()
    {
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->finder = $this->prophesize(FinderInterface::class);

        $this->facade = new Facade(
            $this->em->reveal(),
            $this->finder->reveal()
        );
    }

    public function testProcess()
    {
        $object = new \stdClass();
        $duplicates = [
            $this->prophesize(DuplicateInterface::class)->reveal(),
            $this->prophesize(DuplicateInterface::class)->reveal(),
        ];

        $this->finder->search($object)->willReturn($duplicates);
        $this->em->persist(Argument::any())
            ->shouldBeCalled();

        $this->em->flush()->shouldBeCalled();

        self::assertEquals(
            $duplicates,
            $this->facade->search($object)
        );
    }
}

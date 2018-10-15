<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Tests\Factory;

use DMK\DuplicateCheckBundle\Factory\EntityFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

class EntityFactoryTest extends TestCase
{
    protected function setup()
    {
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->factory = new EntityFactory($this->em->reveal());
    }

    public function test_create_object()
    {
        $original = new \stdClass();
        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getIdentifierValues($original)->willReturn(['id' => 1]);

        $this->em->getClassMetadata('stdClass')->willReturn($metadata);
        $object = $this->factory->create($original, 1.0);

        $this->assertEquals($original, $object->getObject());
        $this->assertEquals(1.0, $object->getWeight());
        $this->assertEquals('stdClass', $object->getClass());
    }
}

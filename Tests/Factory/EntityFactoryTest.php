<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Tests\Factory;

use DMK\DuplicateCheckBundle\Factory\EntityFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Oro\Bundle\EntityBundle\Provider\EntityNameResolver;
use PHPUnit\Framework\TestCase;

class EntityFactoryTest extends TestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $em;
    /**
     * @var EntityFactory
     */
    private $factory;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $resolver;

    protected function setup()
    {
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->resolver
            = $this->prophesize(EntityNameResolver::class);

        $this->factory = new EntityFactory($this->resolver->reveal(), $this->em->reveal());
    }

    public function test_create_object()
    {
        $original = new \stdClass();
        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getIdentifierValues($original)->willReturn(['id' => 1]);

        $this->resolver->getName($original)->willReturn('foo');

        $this->em->getClassMetadata('stdClass')->willReturn($metadata);
        $object = $this->factory->create($original, 1.0);

        $this->assertEquals($original, $object->getObject());
        $this->assertEquals(1.0, $object->getWeight());
        $this->assertEquals('stdClass', $object->getClass());
        $this->assertSame('foo', $object->getName());
    }
}

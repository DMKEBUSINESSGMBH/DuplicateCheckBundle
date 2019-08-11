<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Tests\EventListener;

use DMK\DuplicateCheckBundle\Async\Topics;
use DMK\DuplicateCheckBundle\EventListener\CheckListener;
use DMK\DuplicateCheckBundle\Provider\ConfigProvider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use PHPUnit\Framework\TestCase;

class CheckListenerTest extends TestCase
{
    private $listener;

    private $producer;

    private $configProvider;

    protected function setup()
    {
        $this->producer = $this->prophesize(MessageProducerInterface::class);
        $this->configProvider = $this->prophesize(ConfigProvider::class);
        $this->listener = new CheckListener(
            $this->producer->reveal(),
            $this->configProvider->reveal()
        );
    }

    public function test_on_flush()
    {
        $object = new \stdClass();
        $uwo = $this->prophesize(UnitOfWork::class);
        $em = $this->prophesize(EntityManagerInterface::class);
        $em->getUnitOfWork()->willReturn($uwo->reveal());
        $args = new OnFlushEventArgs($em->reveal());

        $uwo->getScheduledEntityInsertions()->willReturn([$object]);
        $uwo->getScheduledEntityUpdates()->willReturn([]);

        $this->configProvider->isEntityEnabled('stdClass')
            ->willReturn(true);

        $this->listener->onFlush($args);

        $reflection = new \ReflectionProperty(CheckListener::class, 'objects');
        $reflection->setAccessible(true);

        $this->assertContains($object, $reflection->getValue($this->listener));
    }

    public function test_post_flush()
    {
        $object = new \stdClass();
        $em = $this->prophesize(EntityManagerInterface::class);
        $uwo = $this->prophesize(UnitOfWork::class);
        $em->getUnitOfWork()->willReturn($uwo->reveal());

        $uwo->getScheduledEntityInsertions()->willReturn([$object]);
        $uwo->getScheduledEntityUpdates()->willReturn([]);

        $this->configProvider->isEntityEnabled('stdClass')
            ->willReturn(true);

        $this->listener->onFlush(new OnFlushEventArgs($em->reveal()));

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getIdentifierValues($object)->willReturn(['id' => 1]);
        $em->getClassMetadata('stdClass')->willReturn($metadata->reveal());

        $this->producer->send(Topics::TOPIC_CHECK_SINGLE, [
            'id' => ['id' => 1],
            'class' => 'stdClass',
        ])->shouldBeCalled();

        $args = new PostFlushEventArgs($em->reveal());

        $this->listener->postFlush($args);
    }
}

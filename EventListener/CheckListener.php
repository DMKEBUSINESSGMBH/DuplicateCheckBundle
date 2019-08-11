<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\EventListener;

use DMK\DuplicateCheckBundle\Async\Topics;
use DMK\DuplicateCheckBundle\Provider\ConfigProvider;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;

class CheckListener implements EventSubscriber
{
    /**
     * @var \SplObjectStorage
     */
    private $objects;

    /**
     * @var ConfigProvider
     */
    private $provider;

    /**
     * @var MessageProducerInterface
     */
    private $producer;

    public function __construct(MessageProducerInterface $producer, ConfigProvider $provider)
    {
        $this->producer = $producer;
        $this->provider = $provider;
        $this->objects = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
            Events::postFlush,
        ];
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if (!$this->provider->isEntityEnabled(get_class($entity))) {
                continue;
            }

            $this->objects->attach($entity);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$this->provider->isEntityEnabled(get_class($entity))) {
                continue;
            }

            $this->objects->attach($entity);
        }
    }

    /**
     * @param PostFlushEventArgs $args
     *
     * @throws \Oro\Component\MessageQueue\Transport\Exception\Exception
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        foreach ($this->objects as $object) {
            $class = get_class($object);
            $metadata = $args->getEntityManager()->getClassMetadata($class);

            $this->producer->send(Topics::TOPIC_CHECK_SINGLE, [
                'class' => $class,
                'id' => $metadata->getIdentifierValues($object),
            ]);
        }
    }
}

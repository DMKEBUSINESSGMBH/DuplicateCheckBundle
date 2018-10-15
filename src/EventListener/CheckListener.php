<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\EventListener;

use DMK\DuplicateCheckBundle\Async\Topics;
use DMK\DuplicateCheckBundle\Provider\ConfigProvider;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
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
            Events::postFlush
        ];
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if (!$this->provider->isEntityEnabled(ClassUtils::getClass($entity))) {
                continue;
            }

            $this->objects->attach($entity);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$this->provider->isEntityEnabled(ClassUtils::getClass($entity))) {
                continue;
            }

            $this->objects->attach($entity);
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        foreach ($this->objects as $object) {
            $class = ClassUtils::getClass($object);
            $metadata = $args->getEntityManager()->getClassMetadata($class);

            $this->producer->send(Topics::TOPIC_CHECK_SINGLE, [
                'class' => $class,
                'id' => $metadata->getIdentifierValues($object)
            ]);
        }
    }
}
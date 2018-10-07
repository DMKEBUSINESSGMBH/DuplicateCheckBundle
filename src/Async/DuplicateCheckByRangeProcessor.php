<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Async;

use DMK\DuplicateCheckBundle\Facade;
use Doctrine\ORM\EntityManager;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Job\JobRunner;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Util\JSON;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DuplicateCheckByRangeProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{
    private $registry;

    private $runner;

    private $logger;

    private $facade;

    public function __construct(
        Facade $facade,
        RegistryInterface $registry,
        JobRunner $runner,
        LoggerInterface $logger
    )
    {
        $this->registry = $registry;
        $this->facade = $facade;
        $this->runner = $runner;
        $this->logger = $logger;
    }

    public function process(MessageInterface $message, SessionInterface $session)
    {
        $payload = JSON::decode($message->getBody());

        $result = $this->runner->runDelayed($payload['jobId'], function () use ($message, $payload) {
            if (! isset($payload['entityClass'], $payload['offset'], $payload['limit'])) {
                $this->logger->error('Message is not valid.');

                return false;
            }

            /** @var EntityManager $em */
            if (! $em = $this->registry->getManagerForClass($payload['entityClass'])) {
                $this->logger->error(
                    sprintf('Entity manager is not defined for class: "%s"', $payload['entityClass'])
                );

                return false;
            }

            $identifierFieldName = $em->getClassMetadata($payload['entityClass'])->getSingleIdentifierFieldName();
            $repository = $em->getRepository($payload['entityClass']);

            $ids = $repository->createQueryBuilder('ids')
                ->select('ids.'.$identifierFieldName)
                ->setFirstResult($payload['offset'])
                ->setMaxResults($payload['limit'])
                ->orderBy('ids.'.$identifierFieldName, 'ASC')
                ->getQuery()->getArrayResult()
            ;
            $ids = array_map('current', $ids);

            if (false == $ids) {
                return true;
            }

            $entities = $repository->createQueryBuilder('entity')
                ->where('entity IN(:ids)')
                ->setParameter('ids', $ids)
                ->getQuery()->iterate()
            ;

            foreach ($entities as $entity) {
                $this->facade->perform($entity);
            }

            return true;
        });

        return $result ? self::ACK : self::REJECT;
    }

    public static function getSubscribedTopics()
    {
        return [Topics::TOPIC_CHECK_RANGE];
    }

}
<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Async;

use Doctrine\ORM\EntityManager;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Job\Job;
use Oro\Component\MessageQueue\Job\JobRunner;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Util\JSON;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DuplicateCheckByClassProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{
    const BATCH_SIZE = 1000;

    private $registry;

    private $producer;

    private $runner;

    private $logger;

    public function __construct(RegistryInterface $registry, MessageProducerInterface $producer, JobRunner $runner, LoggerInterface $logger)
    {
        $this->registry = $registry;
        $this->producer = $producer;
        $this->runner = $runner;
        $this->logger = $logger;
    }

    public function process(MessageInterface $message, SessionInterface $session)
    {
        $body = JSON::decode($message->getBody());

        $result = $this->runner->createDelayed($body['jobId'], function(JobRunner $jobRunner) use ($body) {
            /** @var EntityManager $em */
            if (! $em = $this->registry->getManagerForClass($body['entityClass'])) {
                $this->logger->error(
                    sprintf('Entity manager is not defined for class: "%s"', $body['entityClass'])
                );

                return false;
            }

            $entityCount = $em->getRepository($body['entityClass'])
                ->createQueryBuilder('entity')
                ->select('COUNT(entity)')
                ->getQuery()
                ->getSingleScalarResult()
            ;

            $batches = (int) ceil($entityCount / self::BATCH_SIZE);
            for ($i = 0; $i < $batches; $i++) {
                $jobRunner->createDelayed(
                    sprintf('%s:%s:%s', Topics::TOPIC_CHECK_RANGE, $body['entityClass'], $i),
                    function (JobRunner $jobRunner, Job $child) use ($i, $body) {
                        $this->producer->send(Topics::TOPIC_CHECK_RANGE, [
                            'entityClass' => $body['entityClass'],
                            'offset' => $i * self::BATCH_SIZE,
                            'limit' => self::BATCH_SIZE,
                            'jobId' => $child->getId(),
                        ]);
                    }
                );
            }

            return true;
        });

        return $result ? self::ACK : self::REJECT;
    }

    public static function getSubscribedTopics()
    {
        return [Topics::TOPIC_CHECK_CLASS];
    }

}
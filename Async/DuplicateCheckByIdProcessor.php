<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Async;

use DMK\DuplicateCheckBundle\Exception\InvalidArgumentException;
use DMK\DuplicateCheckBundle\Facade;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Job\JobRunner;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Util\JSON;
use Psr\Log\LoggerInterface;

class DuplicateCheckByIdProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{
    /**
     * @var Facade
     */
    private $finder;
    /**
     * @var JobRunner
     */
    private $runner;
    /**
     * @var ObjectManager
     */
    private $om;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Facade          $finder
     * @param ObjectManager   $manager
     * @param JobRunner       $runner
     * @param LoggerInterface $logger
     */
    public function __construct(Facade $finder, ObjectManager $manager, JobRunner $runner, LoggerInterface $logger)
    {
        $this->finder = $finder;
        $this->om = $manager;
        $this->runner = $runner;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function process(MessageInterface $message, SessionInterface $session): string
    {
        try {
            $this->assertEnvironment($message);
        } catch (InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());

            return self::REJECT;
        }

        $body = JSON::decode($message->getBody());

        $jobName = sprintf('duplicate_check|%s', md5($body['class'].serialize($body['id'])));

        $this->runner->runUnique(
            $message->getMessageId(),
            $jobName,
            function () use ($body) {
                $object = $this->om->find($body['class'], $body['id']);

                $this->finder->search($object);

                return true;
            }
        );

        return self::ACK;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedTopics()
    {
        return [Topics::TOPIC_CHECK_SINGLE];
    }

    /**
     * @param MessageInterface $message
     */
    private function assertEnvironment(MessageInterface $message): void
    {
        $body = json_decode($message->getBody(), true);

        if (!$body['class'] ?? null) {
            throw new InvalidArgumentException('The key "class" is missing in message');
        }

        if (!class_exists($body['class'])) {
            throw new InvalidArgumentException(sprintf(
                'The class "%s" does not exists.',
                $body['class']
            ));
        }

        if (!$body['id'] ?? null) {
            throw new InvalidArgumentException('The key "id" is missing in message');
        }
    }
}

<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Async;

use DMK\DuplicateCheckBundle\Exception\InvalidArgumentException;
use DMK\DuplicateCheckBundle\FinderInterface;
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
    private $finder;
    private $runner;
    private $om;
    private $logger;

    public function __construct(FinderInterface $finder, ObjectManager $manager, JobRunner $runner, LoggerInterface $logger)
    {
        $this->finder = $finder;
        $this->om = $manager;
        $this->runner = $runner;
        $this->logger = $logger;
    }

    public function process(MessageInterface $message, SessionInterface $session)
    {
        try {
            $this->assertEnvironment($message);
        } catch (InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());

            return self::REJECT;
        }

        $body = JSON::decode($message->getBody());

        $jobName =  sprintf('duplicate_check|%s', md5($body['class'], serialize($body['id'])));

        $this->runner->runUnique(
            $message->getMessageId(),
            $jobName,
            function() use ($body) {
                $object = $this->om->find($body['class'], $body['id']);

                $this->finder->search($object);
            }
        );

        return self::ACK;
    }

    public static function getSubscribedTopics()
    {
        return [Topics::TOPIC_CHECK_SINGLE];
    }

    private function assertEnvironment(MessageInterface $message)
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

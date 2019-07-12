<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle;

use DMK\DuplicateCheckBundle\Exception\AdapterNotFoundException;
use DMK\DuplicateCheckBundle\Adapter\AdapterInterface;

final class Finder implements FinderInterface
{
    /**
     * @var array
     */
    private $adapters = [];

    public function __construct(array $adapters)
    {
        foreach ($adapters as $adapter) {
            $this->register($adapter);
        }
    }

    /**
     * Register an adapter.
     *
     * @param AdapterInterface $adapter
     */
    public function register(AdapterInterface $adapter): void
    {
        $this->adapters[] = $adapter;
    }

    /**
     * Unregister an adapter from the finder.
     *
     * @param AdapterInterface $adapter
     *
     * @throws AdapterNotFoundException
     */
    public function unregister(AdapterInterface $adapter): void
    {
        if (false === $key = array_search($adapter, $this->adapters, true)) {
            throw new AdapterNotFoundException(sprintf(
                'The adapter "%s" was not registred before.',
                get_class($adapter)
            ));
        }

        unset($this->adapters[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function search($object): iterable
    {
        foreach ($this->getMatchingAdapters($object) as $adapter) {
            yield from $adapter->process($object);
        }
    }

    /**
     * @param object $object
     *
     * @return AdapterInterface[]
     */
    private function getMatchingAdapters($object)
    {
        return array_filter($this->adapters, function (AdapterInterface $adapter) use ($object) {
            return $adapter->supports($object);
        });
    }
}

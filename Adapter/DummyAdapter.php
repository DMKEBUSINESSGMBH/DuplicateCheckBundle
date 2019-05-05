<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Adapter;


class DummyAdapter implements AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($object): iterable
    {
        yield from [];
    }

    /**
     * {@inheritdoc}
     */
    public function supports($object): bool
    {
        return true;
    }

}
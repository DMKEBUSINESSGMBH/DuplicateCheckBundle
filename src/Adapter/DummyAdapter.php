<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Adapter;


class DummyAdapter implements AdapterInterface
{
    public function process($object): iterable
    {
        yield from [];
    }

    public function support($object): bool
    {
        return true;
    }

}
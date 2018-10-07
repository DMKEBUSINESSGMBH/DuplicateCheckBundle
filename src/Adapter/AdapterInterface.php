<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Adapter;


interface AdapterInterface
{
    public function process($object): iterable;

    public function support($object): bool;
}
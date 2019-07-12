<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Tests;

use DMK\DuplicateCheckBundle\Adapter\AdapterInterface;
use DMK\DuplicateCheckBundle\Finder;
use DMK\DuplicateCheckBundle\Model\DuplicateInterface;
use PHPUnit\Framework\TestCase;

class FinderTest extends TestCase
{
    protected function setup()
    {
        $this->adapter = $this->prophesize(AdapterInterface::class);
        $this->finder = new Finder([
            $this->adapter->reveal(),
        ]);
    }

    public function test_register()
    {
        $adapter = $this->prophesize(AdapterInterface::class)->reveal();
        $reflection = new \ReflectionProperty(Finder::class, 'adapters');
        $reflection->setAccessible(true);

        $this->finder->register($adapter);

        $this->assertContains($adapter, $reflection->getValue($this->finder));
    }

    public function test_unregister()
    {
        $reflection = new \ReflectionProperty(Finder::class, 'adapters');
        $reflection->setAccessible(true);

        $this->finder->unregister($this->adapter->reveal());

        $this->assertNotContains($this->adapter->reveal(), $reflection->getValue($this->finder));
    }

    public function test_search()
    {
        $object = new \stdClass();
        $duplicates = [$this->prophesize(DuplicateInterface::class)->reveal()];

        $this->adapter->supports($object)
            ->willReturn(true);

        $this->adapter->process($object)
            ->willReturn($duplicates);

        $this->assertEquals($duplicates, iterator_to_array($this->finder->search($object)));
    }

    public function test_search_without_adapters()
    {
        $object = new \stdClass();

        $this->adapter->supports($object)
            ->willReturn(false);

        $this->adapter->process($object)
            ->shouldNotBeCalled();

        $this->assertEmpty(iterator_to_array($this->finder->search($object)));
    }
}

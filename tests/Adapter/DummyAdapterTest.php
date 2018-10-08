<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Tests\Adapter;

use DMK\DuplicateCheckBundle\Adapter\DummyAdapter;
use PHPUnit\Framework\TestCase;

class DummyAdapterTest extends TestCase
{
    private $adapter;

    protected function setUp()
    {
        $this->adapter = new DummyAdapter();
    }

    public function testSupport()
    {
        $this->assertTrue($this->adapter->support(new \stdClass()));
    }

    public function testProcess()
    {
        $results = $this->adapter->process(new \stdClass());
        $results = iterator_to_array($results);

        $this->assertEmpty($results);
    }
}

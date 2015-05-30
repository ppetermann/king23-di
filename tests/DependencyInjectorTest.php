<?php
namespace King23\DI {

    use Inject\MockImplemented;

    class DependencyInjectorTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException \Exception
         * @expectedExceptionMessage Error: for test there is already an implementation registered
         */
        public function testDoubleRegister()
        {
            $instance = new DependencyInjector();

            // same injector two times should throw exception
            $instance->registerInjector(
                'test',
                function () {
                }
            );
            $instance->registerInjector(
                'test',
                function () {
                }
            );
        }

        public function testInjector()
        {
            $instance = new DependencyInjector();
            $instance->registerInjector(
                \Inject\Mock::class,
                function(){
                    return new MockImplemented();
                }
            );

            $result = $instance->getInstanceOf(\Test\InjectHere::class);
            $this->assertInstanceOf('\Inject\MockImplemented', $result->mockInjected);
            $this->assertTrue($result->test());
        }
    }
}

namespace Inject {

    interface Mock
    {
        public function test();
    }

    class MockImplemented implements Mock
    {
        public function test()
        {
            return true;
        }
    }
}

namespace Test {
    class InjectHere
    {
        public $mockInjected;
        public function __construct(\Inject\Mock $injected)
        {
            $this->mockInjected = $injected;
        }

        public function test()
        {
            return $this->mockInjected->test();
        }
    }
}

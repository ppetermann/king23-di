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
            $instance->register(
                'test',
                function () {
                }
            );
            $instance->register(
                'test',
                function () {
                }
            );
        }

        public function testInjector()
        {
            $instance = new DependencyInjector();
            $instance->register(
                \Inject\Mock::class,
                function(){
                    return new MockImplemented();
                }
            );

            $result = $instance->getInstanceOf(\Test\InjectHere::class);
            $this->assertInstanceOf('\Inject\MockImplemented', $result->mockInjected);
            $this->assertTrue($result->test());
        }

        /**
         * @expectedException \Exception
         * @expectedExceptionMessage no Injector registered for Inject\Something
         */
        public function testNotFound()
        {
            $instance = new DependencyInjector();
            $instance->getInstanceOf(\Test\InjectFail::class);
        }

        public function testSingleton()
        {
            $instance = new DependencyInjector();
            $instance->register(
                \Inject\Mock::class,
                function(){
                    return new MockImplemented();
                }
            );

            $result1 = $instance->getInstanceOf(\Test\InjectHere::class);
            $result2 = $instance->getInstanceOf(\Test\InjectHere::class);
            $this->assertInstanceOf('\Inject\MockImplemented', $result1->mockInjected);
            $this->assertInstanceOf('\Inject\MockImplemented', $result2->mockInjected);
            $this->assertTrue($result1->mockInjected === $result2->mockInjected);
        }

        public function testFactory()
        {
            $instance = new DependencyInjector();
            $instance->registerFactory(
                \Inject\Mock::class,
                function(){
                    return new MockImplemented();
                }
            );

            $result1 = $instance->getInstanceOf(\Test\InjectHere::class);
            $result2 = $instance->getInstanceOf(\Test\InjectHere::class);
            $this->assertInstanceOf('\Inject\MockImplemented', $result1->mockInjected);
            $this->assertInstanceOf('\Inject\MockImplemented', $result2->mockInjected);
            $this->assertTrue($result1->mockInjected !== $result2->mockInjected);
        }
    }
}

namespace Inject {

    interface Mock
    {
        public function test();
    }

    interface Something {

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

    class InjectFail
    {
        public function __construct(\Inject\Something $foobar)
        {

        }
    }
}

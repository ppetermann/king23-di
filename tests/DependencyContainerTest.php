<?php
namespace King23\DI {

    use Inject\MockImplemented;

    class DependencyContainerTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException \Exception
         * @expectedExceptionMessage Error: for test there is already an implementation registered
         */
        public function testDoubleRegister()
        {
            $instance = new DependencyContainer();

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

        /**
         * @expectedException \Exception
         * @expectedExceptionMessage Error: for test there is already an implementation registered
         */
        public function testDoubleRegisterFactory()
        {
            $instance = new DependencyContainer();

            // same injector two times should throw exception
            $instance->register(
                'test',
                function () {
                }
            );
            $instance->registerFactory(
                'test',
                function () {
                }
            );
        }

        public function testInjector()
        {
            $instance = new DependencyContainer();
            $instance->register(
                \Inject\Mock::class,
                function () {
                    return new MockImplemented();
                }
            );

            $this->assertInstanceOf(
                \Inject\MockImplemented::class,
                $instance->getInstanceOf(\Inject\Mock::class)
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
            $instance = new DependencyContainer();
            $instance->getInstanceOf(\Test\InjectFail::class);
        }

        public function testSingleton()
        {
            $instance = new DependencyContainer();
            $instance->register(
                \Inject\Mock::class,
                function () {
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
            $instance = new DependencyContainer();
            $instance->registerFactory(
                \Inject\Mock::class,
                function () {
                    return new MockImplemented();
                }
            );

            $result1 = $instance->getInstanceOf(\Test\InjectHere::class);
            $result2 = $instance->getInstanceOf(\Test\InjectHere::class);
            $this->assertInstanceOf('\Inject\MockImplemented', $result1->mockInjected);
            $this->assertInstanceOf('\Inject\MockImplemented', $result2->mockInjected);
            $this->assertTrue($result1->mockInjected !== $result2->mockInjected);
        }

        /**
         * @expectedException \Exception
         * @expectedExceptionMessage parameters for contstructor contains field without typehint
         */
        public function testNoHint()
        {
            $instance = new DependencyContainer();
            $instance->getInstanceOf(\Test\InjectNoHint::class);
        }

        public function testSelfInject()
        {
            $instance = new DependencyContainer();
            $this->assertTrue(
                $instance->getInstanceOf(\Test\SelfInject::class)->container === $instance
            );
        }
    }

}

namespace Inject {

    interface Mock
    {
        public function test();
    }

    interface Something
    {

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

    use King23\DI\ContainerInterface;

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

    class InjectNoHint
    {
        public function __construct($foo)
        {

        }
    }

    class SelfInject
    {
        public $container;

        public function __construct(ContainerInterface $container)
        {
            $this->container = $container;
        }
    }
}

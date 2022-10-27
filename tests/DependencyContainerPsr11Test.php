<?php

namespace King23\DI {

    use Inject\MockImplemented;
    use PHPUnit\Framework\TestCase;

    class DependencyContainerPsr11Test extends TestCase
    {
        public function testDoubleRegister()
        {
            $this->expectException(\King23\DI\Exception\AlreadyRegisteredException::class);
            $this->expectExceptionMessage("Error: for test there is already an implementation registered");

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

        public function testDoubleRegisterFactory()
        {
            $this->expectException(\King23\DI\Exception\AlreadyRegisteredException::class);
            $this->expectExceptionMessage("Error: for test there is already an implementation registered");

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

            $this->assertTrue($instance->has(\Inject\Mock::class));

            $this->assertInstanceOf(
                \Inject\MockImplemented::class,
                $instance->get(\Inject\Mock::class)
            );

            $result = $instance->get(\Test\InjectHere::class);
            $this->assertInstanceOf('\Inject\MockImplemented', $result->mockInjected);
            $this->assertTrue($result->test());
        }

        public function testClassNotFound()
        {
            $this->expectExceptionMessage("Class/Interface not found: '\Test\InjectFail'");
            $this->expectException(\King23\DI\Exception\NotFoundException::class);
            $instance = new DependencyContainer();
            $this->assertFalse($instance->has('\Test\InjectFail'));
            $instance->get('\Test\InjectFail');
        }

        public function testHintNotFound()
        {
            $this->expectExceptionMessage("Class/Interface not found: 'Inject\DoesntExist'");
            $this->expectException(\King23\DI\Exception\NotFoundException::class);   
            $instance = new DependencyContainer();
            $this->assertTrue($instance->has('\Test\HintNotFound'));
            $instance->get('\Test\HintNotFound');
        }

        public function testInjectorNotFound()
        {
            $this->expectExceptionMessage("no Injector registered for interface: 'Inject\Something'");
            $this->expectException(\King23\DI\Exception\NotFoundException::class);
            $instance = new DependencyContainer();
            $this->assertTrue($instance->has('\Test\InjectorNotFound'));
            $instance->get('\Test\InjectorNotFound');
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

            $this->assertTrue($instance->has(\Inject\Mock::class));
            $this->assertTrue($instance->has(\Test\InjectHere::class));

            $result1 = $instance->get(\Test\InjectHere::class);
            $result2 = $instance->get(\Test\InjectHere::class);
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

            $this->assertTrue($instance->has(\Inject\Mock::class));
            $this->assertTrue($instance->has(\Test\InjectHere::class));

            $result1 = $instance->get(\Test\InjectHere::class);
            $result2 = $instance->get(\Test\InjectHere::class);
            $this->assertInstanceOf('\Inject\MockImplemented', $result1->mockInjected);
            $this->assertInstanceOf('\Inject\MockImplemented', $result2->mockInjected);
            $this->assertTrue($result1->mockInjected !== $result2->mockInjected);
        }

        public function testNoHint()
        {
            $this->expectExceptionMessage("parameters for constructor contains field without typehint");
            $this->expectException(\King23\DI\Exception\NotFoundException::class);
            $instance = new DependencyContainer();
            $instance->get(\Test\InjectNoHint::class);
        }

        public function testSelfInject()
        {
            $instance = new DependencyContainer();
            $this->assertTrue(
                $instance->get(\Test\SelfInject::class)->containerPsr === $instance
            );
            $this->assertTrue(
                $instance->get(\Test\SelfInject::class)->containerKing23 === $instance
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

    use Psr\Container\ContainerInterface;

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

    class InjectNoHint
    {
        public function __construct($foo)
        {

        }
    }

    class SelfInject
    {
        public $containerPsr;
        public $containerKing23;

        /**
         * SelfInject constructor.
         * @param ContainerInterface $containerPsr
         * @param \King23\DI\ContainerInterface $containerKing23
         */
        public function __construct(ContainerInterface $containerPsr, \King23\DI\ContainerInterface $containerKing23)
        {
            $this->containerPsr = $containerPsr;
            $this->containerKing23 = $containerKing23;
        }
    }



    class HintNotFound
    {
        public function __construct(\Inject\DoesntExist $doesntExist)
        {
        }
    }

    class InjectorNotFound
    {
        public function __construct(\Inject\Something $something)
        {
        }
    }
}

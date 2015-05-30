<?php
namespace Inject {

    require_once "vendor/autoload.php";

    interface Me
    {
        public function hello();
    }

    class MEImpl implements Me
    {
        public function hello()
        {
            echo "hello";
        }
    }
}

namespace Test {
    class TestMe
    {
        public function __construct(\Inject\Me $injected)
        {
            $injected->hello();
        }
    }
}

namespace Fun {
    use Inject\Me;
    use Inject\MEImpl;
    use Test\TestMe;

    $injector = new \King23\DI\DependencyInjector();
    $injector->registerInjector(
        Me::class,
        function () {
            return new MEImpl();
        }
    );

    $instance = $injector->getInstanceOf(TestMe::class);
    var_dump($instance);
}

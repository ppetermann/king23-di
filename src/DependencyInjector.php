<?php
namespace King23\DI;

class DependencyInjector implements ContainerInterface
{
    protected $injectors = [];

    /**
     * Constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $that = $this;
        // we register ourselves, so this container can be injected too
        $this->register(
            ContainerInterface::class,
            function () use ($that) {
                return $that;
            }
        );
    }

    /**
     * check if for $interface there is an existing service to inject
     *
     * @param $interface
     * @return bool
     */
    protected function hasServiceFor($interface)
    {
        return isset($this->injectors[$interface]);
    }

    /**
     * gets a service instance for $interface - singletoned!
     *
     * @param string $interface
     * @return mixed
     * @throws \Exception
     */
    protected function getServiceInstanceFor($interface)
    {
        return $this->injectors[$interface]();
    }

    /**
     * register an service implementation as a singleton (shared instance)
     *
     * @param $interface
     * @param callable $implementation
     * @throws \Exception
     */
    public function register($interface, callable $implementation)
    {
        if (isset($this->injectors[$interface])) {
            throw new \Exception("Error: for $interface there is already an implementation registered");
        }

        // wraps the implementation in a singleton
        $this->injectors[$interface] = function () use ($implementation) {
            static $instance;
            if (is_null($instance)) {
                $instance = $implementation();
            }

            return $instance;
        };
    }

    /**
     * register a service implementation as a factory
     *
     * @param $interface
     * @param callable $implementation
     * @throws \Exception
     */
    public function registerFactory($interface, callable $implementation)
    {
        if (isset($this->injectors[$interface])) {
            throw new \Exception("Error: for $interface there is already an implementation registered");
        }

        $this->injectors[$interface] = $implementation;
    }

    /**
     * @param string $classname fully qualified classname
     * @return object
     * @throws \Exception
     */
    public function getInstanceOf($classname)
    {
        // first we check if this is maybe a known service
        if ($this->hasServiceFor($classname)) {
            return $this->getServiceInstanceFor($classname);
        }

        // alright, this was not one of our known services, so we assume
        // we are supposed to play factory for $classname
        $reflector = new \ReflectionClass($classname);
        $args = [];

        /** @var \ReflectionParameter $parameter */
        foreach ($reflector->getConstructor()->getParameters() as $parameter) {
            if (is_null($parameter->getClass())) {
                throw new \Exception("parameters for contstructor contains field without typehint");
            }
            $paramClass = $parameter->getClass()->getName();
            if ($this->hasServiceFor($paramClass)) {
                $args[] = $this->getServiceInstanceFor($paramClass);
            } else {
                throw new \Exception("no Injector registered for $paramClass");
            }
        }

        return $reflector->newInstanceArgs($args);
    }
}

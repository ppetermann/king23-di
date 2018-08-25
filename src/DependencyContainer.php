<?php

namespace King23\DI;

use King23\DI\Exception\AlreadyRegisteredException;
use King23\DI\Exception\NotFoundException;

/**
 * Class DependencyContainer
 * @package King23\DI
 */
class DependencyContainer implements ContainerInterface
{
    protected $injectors = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $that = $this;
        // we register ourselves, so this container can be injected too
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->register(
            \Psr\Container\ContainerInterface::class,
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
     * @throws AlreadyRegisteredException
     */
    public function register($interface, callable $implementation)
    {
        if (isset($this->injectors[$interface])) {
            throw new AlreadyRegisteredException("Error: for $interface there is already an implementation registered");
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
     * @throws AlreadyRegisteredException
     */
    public function registerFactory($interface, callable $implementation)
    {
        if (isset($this->injectors[$interface])) {
            throw new AlreadyRegisteredException("Error: for $interface there is already an implementation registered");
        }

        $this->injectors[$interface] = $implementation;
    }

    /**
     * @param string $classname fully qualified classname
     * @return object
     * @throws NotFoundException
     * @throws \ReflectionException
     */
    public function get($classname)
    {
        // first we check if this is maybe a known service
        if ($this->hasServiceFor($classname)) {
            return $this->getServiceInstanceFor($classname);
        }

        // alright, this was not one of our known services, so we assume
        // we are supposed to play factory for $classname

        // lets see if the class actually exists first
        if (!class_exists($classname, true) && !interface_exists($classname, true)) {
            throw new NotFoundException("Class/Interface not found: '$classname'");
        }

        $reflector = new \ReflectionClass($classname);

        $args = [];

        $constructor = $reflector->getConstructor();

        // if there is no constructor, we don't need to inject anything
        if (!is_null($constructor)) {
            /** @var \ReflectionParameter $parameter */
            foreach ($constructor->getParameters() as $parameter) {
                $args[] = $this->handleReflectionParameter($parameter, $classname);
            }
        }

        if ($reflector->isInterface()) {
            throw new NotFoundException("no Injector registered for interface: '$classname'");
        }

        return $reflector->newInstanceArgs($args);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        // if we have a service registered, we can assume true
        if($this->hasServiceFor($id)) {
            return true;
        }

        // if we don't have as service registered, we might still pull one from the hat
        // so lets at least check if the class would be available
        return class_exists($id, true);
    }

    /**
     * @param \ReflectionParameter $parameter
     * @param string $classname
     * @return mixed|object
     * @throws NotFoundException
     * @throws \ReflectionException
     */
    private function handleReflectionParameter(\ReflectionParameter $parameter, $classname)
    {
        try {
            if (is_null($parameter->getClass())) {
                throw new NotFoundException("parameters for constructor contains field without typehint");
            }
        } catch (\ReflectionException $reflectionException) {
            throw new NotFoundException("can't reflect parameter '{$parameter->name}' of '$classname'", 0, $reflectionException);
        }
        $paramClass = $parameter->getClass()->getName();
        if ($this->hasServiceFor($paramClass)) {
            return $this->getServiceInstanceFor($paramClass);
        } else {
            return $this->get($paramClass);
        }
    }
}

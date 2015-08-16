<?php
namespace King23\DI;

use King23\DI\Exception\AlreadyRegisteredException;

interface ContainerInterface
{
    /**
     * register an service implementation as a singleton (shared instance)
     * @param $interface
     * @param callable $implementation
     * @return void
     * @throws AlreadyRegisteredException
     */
    public function register($interface, callable $implementation);

    /**
     * register a service implementation as a factory
     *
     * @param $interface
     * @param callable $implementation
     * @return void
     * @throws AlreadyRegisteredException
     */
    public function registerFactory($interface, callable $implementation);

    /**
     * should create an instance of the given $classname, injecting interfaces to the constructor.
     *
     * @param string $classname fully qualified classname
     * @return object
     * @throws AlreadyRegisteredException
     */
    public function getInstanceOf($classname);
}

<?php
namespace King23\DI;

interface ContainerInterface
{
    /**
     * register an service implementation as a singleton (shared instance)
     * @param $interface
     * @param callable $implementation
     * @return void
     * @throws \Exception
     */
    public function register($interface, callable $implementation);

    /**
     * register a service implementation as a factory
     *
     * @param $interface
     * @param callable $implementation
     * @return void
     * @throws \Exception
     */
    public function registerFactory($interface, callable $implementation);

    /**
     * should create an instance of the given $classname, injecting interfaces to the constructor.
     *
     * @param string $classname fully qualified classname
     * @return object
     * @throws \Exception
     */
    public function getInstanceOf($classname);
}

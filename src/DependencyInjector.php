<?php
namespace King23\DI;

class DependencyInjector
{
    protected $injectors = [];
    protected $instances = [];

    public function __construct()
    {
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
        if (!$this->hasServiceFor($interface)) {
            throw new \Exception("tried to get non existing service");
        }

        if (!isset($this->instances[$interface])) {
            $this->instances[$interface] = $this->injectors[$interface]();
        }
        return $this->instances[$interface];
    }

    /**
     * @param $interface
     * @param callable $implementation
     * @throws \Exception
     */
    public function registerInjector($interface, callable $implementation)
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
            if(is_null($parameter->getClass())) {
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

    public function injectTo(Injectable $object)
    {

    }

}

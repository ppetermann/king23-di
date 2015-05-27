<?php
namespace King23\DependencyInjection;

class DependencyInjector
{
	use RegistryAwareTrait;

	public function __construct()
	{
	}
	
	public function injectTo($object)
	{
		$injectors = $this->getRegistry()->injectors;
		$reflector = new \ReflectionObject($object);
		
		foreach ($reflector->getInterfaces() as $interface)
		{
			if (
				$interface->isSubclassOf(\King23\DependencyInjection\AwarenessInterface) 
				&&  isset($injectors[$interface->getName()]) {
			 
				$object = $injectors[$interface->getName()]->injectTo($object);
			}
		}
		return $object;
	}
	
}

interface RegistryAwareInterface extends \King23\DependencyInjection\AwarenessInterface
{
	public function setRegistry(\King23\Core\Registry $registry);
	public function getRegistry();
}

interface InjectorInterface 
{
	public function injectTo($object);
}

trait RegistryAwareTrait 
{
	
	protected $registryAwareInstance;

	public function setRegistry(\King23\Core\Registry $registry)
	{
		$this->registryAwareInstance = $registry;
	}
	
	public function getRegistry()
	{
		return $this->registryAwareInstance;
	}
}


class RegistryAwareInjector implements \King23\DependencyInjection\InjectorInterface
{
	public function injectTo($object)
	{
		// since the registry is a singleton, 
		// we can do this dirty little hack
		$object->setRegistry(King23\Core\Registry::getInstance());
		
		return $object;
	}
}
[![License](https://poser.pugx.org/king23/di/license.png)](https://packagist.org/packages/king23/di)
[![Latest Stable Version](https://poser.pugx.org/king23/di/v/stable.png)](https://packagist.org/packages/king23/di)
[![Total Downloads](https://poser.pugx.org/king23/di/downloads.png)](https://packagist.org/packages/king23/di)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ppetermann/king23-di/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ppetermann/king23-di/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/ppetermann/king23-di/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ppetermann/king23-di/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f6c34f05-4105-43f1-b84e-b27376f58106/mini.png)](https://insight.sensiolabs.com/projects/f6c34f05-4105-43f1-b84e-b27376f58106)
# King23/DI, a dependency injection lib for King23
Copyright (C) 2015 - 2018 by Peter Petermann
All rights reserved.

## LICENSE
King23/DI is licensed under a MIT style license, see LICENSE.md 
for further information

## REQUIREMENTS
- PHP 5.6 (might run on earlier versions, untested)
- LINUX / MAC OS X (might run on windows, untested)

## USAGE

### READTHIS
This DI container implements Psr-11, and can be used in the way Psr-11 describes it, however there are two things you should be aware of:
 * Psr-11 states that callers shouldn't assume that the structure of the string carries semantic meaning. However it is **recommended** to use the interface that describes a service to register and retrieve services when using King23\DI, this is generally a good practise as it avoids naming confusions, and specially a good idea with this container, as that allows its auto-wiring through type-hinting to take care of injecting the right services, so you don't have to create descriptions for every single class you want to use this with.
 * Psr-11's has method is supposed to return if an id is registered with the DI Container, for the way the auto-wiring works, as well as the using the interfaces (see above) the behavior is slightly different here - King23\DI will also check if the id handed over is an existing class, which will be loaded then, so it will return true if the identifier exists, or if it doesn't it will check if itcan auto-instantiate the handed class/interface name, assuming thats what the identifier is.

### Installation
install using composer:

- http://getcomposer.org
- within your project: `php composer.phar require king23/di`

**Hint**: King23/DI follows [Semantic Versioning v2.0.0](http://semver.org/spec/v2.0.0.html), so you can set your version in composer to something like "~1.0"

### Usage
Basically the container offers 4 Methods:

**Hint**: King23\DI uses $classname, as the parameter name, however this is equivalent to Psr-11's $id

 - void register(string $interface, callable $implementation) 
 - void registerFactory(string $interface, callable $implementation)
 - mixed get(string $classname)
 - bool has(string $classname)

**register($interface, $implementation)** is used to register dependencies for injection, while $interface can be any string in theory, it is meant to hold the name of an interface. The callable is supposed to be a method which should return a new instance of the interface (there is really nothing build in to stop you from using arbitrary strings, but the automated injection in the constructors when using getInstanceOf is using the type hints of the parameters. If you use any other string you can't have it automatic injected - which kind of voids the point. That said, it still can be of use when integrating King23/DI with frameworks that require specific keys in the DI). The object returned here will only be instanced once, further calls will return the same instance. 

**registerFactory($interface, $implementation)** works the same way register() does, except that each time an instance of $interface is requested a new instance will be created.

**get($classname)**, will return an object of type $classname, which gets the dependencies injected that are defined by the interfaces used in the type hints of its constructor. If called with a class/interfacename that has been used as a key in register/registerFactory, it will actually return an instance of the registered service.  (psr-11)

**has($classname)**, will return a boolean checking if the class/id is actually available (psr-11)

### Examples
```php

    <?php
    // get an Instance of the container
    $container = \King23\DI\DependencyContainer();
   
    // register a singleton service
    $container->register(\ExampleInterface::class, function() {
        return new \ExampleImplementation();   
    });
   
    // now that there is a service registered for \ExampleInterface, we can actually use it
    var_dump($container->get(\ExampleInterface::class)); // will dump an instance of \ExampleImplementation
   
    /** 
     * A simple class with a constructor that allows to inject an instance of \ExampleInterface
     */
    class Foobar 
    {
        protected $example;

        public function __construct(\ExampleInterface $example)
        {
            $this->example = $example;
        }
        
        public function dumpExample()
        {
            var_dump($this->example);
        }
    }
    
    $object = $container->get(Foobar::class); // this line would cause $object to be an instance of Foobar
                                                      // having a protected member $example, that holds a reference
                                                      // to the \ExampleImplementation
   
    $object->dumpExample(); // will dump an instance of \ExampleImplementation
   

```

## LINKS
- [Homepage](http://king23.net)
- [Github](http://github.com/ppetermann/king23-di)
- [Twitter](http://twitter.com/ppetermann)
- [IRC](irc://irc.theairlock.net:6667/King23) bot will join and put in commit messages on commits there 
- [Psr-11](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md)

## CONTACT
- Peter Petermann <ppetermann80@googlemail.com> 

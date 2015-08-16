[![License](https://poser.pugx.org/king23/di/license.png)](https://packagist.org/packages/king23/di)
[![Latest Stable Version](https://poser.pugx.org/king23/di/v/stable.png)](https://packagist.org/packages/king23/di)
[![Total Downloads](https://poser.pugx.org/king23/di/downloads.png)](https://packagist.org/packages/king23/di)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ppetermann/king23-di/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ppetermann/king23-di/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/ppetermann/king23-di/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ppetermann/king23-di/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f6c34f05-4105-43f1-b84e-b27376f58106/mini.png)](https://insight.sensiolabs.com/projects/f6c34f05-4105-43f1-b84e-b27376f58106)
# King23/DI, a dependency injection lib for King23
Copyright (C) 2015 by Peter Petermann
All rights reserved.

## LICENSE
King23/DI is licensed under a MIT style license, see LICENSE.md 
for further information

## REQUIREMENTS
- PHP 5.6 (might run on earlier versions, untested)
- LINUX / MAC OS X (might run on windows, untested)

## USAGE

### Installation
install using composer:

- http://getcomposer.org
- within your project: `php composer.phar require king23/di`

**Hint**: King23/DI follows [Semantic Versioning v2.0.0](http://semver.org/spec/v2.0.0.html), so you can set your version in composer to something like "~1.0"

## Usage
Basically the container offers 3 Methods:

 - void register(string $interface, callable $implementation) 
 - void registerFactory(string $interface, callable $implementation)
 - mixed getInstanceOf(string $classname)

**register($interface, $implementation)** is used to register dependencies for injection, while interface can be any string it is meant to hold the name of an interface. the callable is supposed to be a method which will return a new instance of the interface (the interface is NOT enforced, hence you *could* return something else, but you **shouldn't** do that.). The object returned here will only be instanced once, hence it is basically a singleton.

**registerFactory($interface, $implementation)** works the same way register() does, except that each time an instance of $interface is requested a new instance will be created.

**getInstanceOf($classname)**, will return an object of type $classname, which gets the dependencies injected that are defined by the interfaces used in the type hints of its constructor. If called with a class/interfacename that has been used as a key in register/registerFactory, it will actually return an instance of the registered service.  

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
    var_dump($container->getInstance(\ExampleInterface::class)); // will dump an instance of \ExampleImplementation
   
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
    
    $object = $container->getInstance(Foobar::class); // this line would cause $object to be an instance of Foobar
                                                      // having a protected member $example, that holds a reference
                                                      // to the \ExampleImplementation
   
    $object->dumpExample(); // will dump an instance of \ExampleImplementation
   

```

## LINKS
- [Homepage](http://king23.net)
- [Github](http://github.com/ppetermann/king23-di)
- [Twitter](http://twitter.com/ppetermann)
- [IRC](irc://irc.coldfront.net:6667/King23) bot will join and put in commit messages on commits there 

## CONTACT
- Peter Petermann <ppetermann80@googlemail.com> 

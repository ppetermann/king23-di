<?php
namespace King23\DI\Exception;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Class NotFoundException, thrown when a dependency is not found
 *
 * @package King23\DI\Exception
 */
class NotFoundException extends DIException implements NotFoundExceptionInterface
{

}

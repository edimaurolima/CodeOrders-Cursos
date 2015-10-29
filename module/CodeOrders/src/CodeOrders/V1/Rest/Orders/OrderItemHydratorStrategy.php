<?php
/**
 * Created by PhpStorm.
 * User: edimauro
 * Date: 23/10/15
 * Time: 16:25
 */

namespace CodeOrders\V1\Rest\Orders;


use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

class OrderItemHydratorStrategy implements StrategyInterface
{
    /**
     * @var ClassMethods
     */
    private $hydrator;

    public function __construct(ClassMethods $hydrator)
    {

        $this->hydrator = $hydrator;
    }

    /**
     * Converts the given value so that it can be extracted by the hydrator.
     *
     * @param mixed $value The original value.
     * @param object $object (optional) The original object for context.
     * @return mixed Returns the value that should be extracted.
     */
    public function extract($items)
    {
        $data = [];

        foreach ($items as $item)
        {
            $data[] = $this->hydrator->extract($item);
        }

        return $data;
    }

    /**
     * Converts the given value so that it can be hydrated by the hydrator.
     *
     * @param mixed $value The original value.
     * @param array $data (optional) The original data for context.
     * @return mixed Returns the value that should be hydrated.
     */
    public function hydrate($value)
    {
        throw new \RuntimeException("Hydration is not Supported");
    }
}
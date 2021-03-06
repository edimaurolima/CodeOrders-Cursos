<?php
/**
 * Created by PhpStorm.
 * User: edimauro
 * Date: 26/10/15
 * Time: 09:28
 */

namespace CodeOrders\V1\Rest\Orders;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OrdersServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $orderRepository = $serviceLocator->get('CodeOrders\\V1\\Rest\\Orders\\OrdersRepository');
        $userRepository = $serviceLocator->get('CodeOrders\\V1\\Rest\\Users\\UsersRepository');
        $productRepository = $serviceLocator->get('CodeOrders\\V1\\Rest\\Products\\ProductsRepository');

        return new OrdersService($orderRepository, $userRepository, $productRepository);
    }

}
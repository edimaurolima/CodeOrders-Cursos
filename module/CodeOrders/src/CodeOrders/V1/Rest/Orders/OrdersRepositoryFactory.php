<?php
/**
 * Created by PhpStorm.
 * User: edimauro
 * Date: 09/10/15
 * Time: 14:11
 */

namespace CodeOrders\V1\Rest\Orders;


use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

class OrdersRepositoryFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dbAdapter = $serviceLocator->get('DbAdapter');

        $hydrator = new HydratingResultSet(new ClassMethods(), new OrdersEntity());

        $tableGateway = new TableGateway('orders', $dbAdapter, null, $hydrator);

        $ordersItemTableGateway = $serviceLocator->get('CodeOrders\\V1\\Rest\\Orders\\OrderItemTableGateway');
        $userRepository = $serviceLocator->get('CodeOrders\\V1\\Rest\\Users\\UsersRepository');

        $clientTableGateway = $serviceLocator->get('CodeOrders\\V1\\Rest\\Clients\\ClientsTableGateway');

        return new OrdersRepository($tableGateway, $ordersItemTableGateway, $userRepository, $clientTableGateway);

    }
}
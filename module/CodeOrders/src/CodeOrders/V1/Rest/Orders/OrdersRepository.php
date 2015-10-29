<?php
/**
 * Created by PhpStorm.
 * User: edimauro
 * Date: 09/10/15
 * Time: 14:08
 */

namespace CodeOrders\V1\Rest\Orders;


use Zend\Console\Request;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Paginator\Adapter\DbTableGateway;
use Zend\Stdlib\Hydrator\ObjectProperty;
use ZendDeveloperTools\Collector\DbCollector;
use CodeOrders\V1\Rest\Users\UsersRepository;

class OrdersRepository
{
    /**
     * @var AbstractTableGateway
     */
    private $TableGateway;
    /**
     * @var AbstractTableGateway
     */
    private $OrderItemTableGateway;

    private $UsersRepository;

    public function __construct (AbstractTableGateway $TableGateway, AbstractTableGateway $OrderItemTableGateway, UsersRepository $usersRepository)
    {
        $this->TableGateway = $TableGateway;
        $this->OrderItemTableGateway = $OrderItemTableGateway;
        $this->UsersRepository = $usersRepository;
    }

    public function getUsersRepository()
    {
        return $this->UsersRepository;
    }

    public function findAll()
    {
        $hydrator = new ClassMethods();
        $hydrator->addStrategy('items', new OrderItemHydratorStrategy(new ClassMethods()));

        $orders = $this->TableGateway->select();

        $res = [];

        foreach ($orders as $order)
        {
            $items = $this->OrderItemTableGateway->select(['order_id' => $order->getId()]);
            foreach ($items as $item)
            {
                $order->AddItem($item);
            }
            $data = $hydrator->extract($order);
            $res[] = $data;
        }

        $arrayAdapter = new ArrayAdapter($res);
        $ordersColletion = new OrdersCollection($arrayAdapter);

        return $ordersColletion;
    }

    public function insert( array $data)
    {
        $this->TableGateway->insert($data);

        $id = $this->TableGateway->getLastInsertValue();

        return $id;
    }

    public function insertItem (array $data)
    {
        $this->OrderItemTableGateway->insert($data);
        return $this->OrderItemTableGateway->getLastInsertValue();
    }

    public function getTableGateway()
    {
        return $this->TableGateway;
    }

    public function find($id)
    {
        $hydrator = new ClassMethods();
        $hydrator->addStrategy('items', new OrderItemHydratorStrategy(new ClassMethods()));

        $order = $this->TableGateway->select(['id' => (int)$id])->current();

        $res = [];

        $items = $this->OrderItemTableGateway->select(['order_id' => $order->getId()]);
        foreach ($items as $item)
        {
            $order->AddItem($item);
        }
        $data = $hydrator->extract($order);
        $res[] = $data;

        $arrayAdapter = new ArrayAdapter($res);
        $ordersColletion = new OrdersCollection($arrayAdapter);

        return $ordersColletion;
    }

    public function deleteItem($idOrder)
    {
        $this->OrderItemTableGateway->delete(['order_id'=> $idOrder]);
    }

    public function delete($id)
    {
        $this->TableGateway->delete(['id' => (int)$id]);
        return true;
    }


    public function update ($id, $data)
    {
        $this->TableGateway->update($data, array('id'=> $id));

        return $data;
    }

    public function findAllIdUsuario($idUsuario)
    {
        return $this->TableGateway->select(['user_id'=> $idUsuario]);
    }

    public function findByIdUsuario($id, $idUsuario)
    {
        $hydrator = new ClassMethods();
        $hydrator->addStrategy('items', new OrderItemHydratorStrategy(new ClassMethods()));

        $order = $this->TableGateway->select(['id' => (int)$id, 'user_id' => $idUsuario])->current();

        $res = [];

        $items = $this->OrderItemTableGateway->select(['order_id' => $order->getId()]);
        foreach ($items as $item)
        {
            $order->AddItem($item);
        }
        $data = $hydrator->extract($order);
        $res[] = $data;

        $arrayAdapter = new ArrayAdapter($res);
        $ordersColletion = new OrdersCollection($arrayAdapter);

        return $ordersColletion;
    }
}
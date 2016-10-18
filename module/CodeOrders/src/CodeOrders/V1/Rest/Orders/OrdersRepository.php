<?php
/**
 * Created by PhpStorm.
 * User: edimauro
 * Date: 09/10/15
 * Time: 14:08
 */

namespace CodeOrders\V1\Rest\Orders;


use CodeOrders\V1\Rest\Clients\ClientsRepository;
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
    /**
     * @var AbstractTableGateway
     */
    private $clientsTableGateway;

    public function __construct (AbstractTableGateway $TableGateway, AbstractTableGateway $OrderItemTableGateway, UsersRepository $usersRepository, AbstractTableGateway $clientsTableGateway)
    {
        $this->TableGateway = $TableGateway;
        $this->OrderItemTableGateway = $OrderItemTableGateway;
        $this->UsersRepository = $usersRepository;
        $this->clientsTableGateway = $clientsTableGateway;
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
                $order->addItem($item);
            }
            $data = $hydrator->extract($order);
            $res[] = $data;
        }

        //return  $res;


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
        $resultSet = $this->TableGateway->select(['id' => (int)$id]);

        if ($resultSet->count() == 1) {
            $hydrator =  new ClassMethods();
            $hydrator->addStrategy('items', new OrderItemHydratorStrategy(new ClassMethods()));
            $order = $resultSet->current();

            $client = $this->clientsTableGateway->select(['id' => $order->getClientId()])->current();

            $sql = $this->OrderItemTableGateway->getSql();
            $select = $sql->select();

            $select->join(
                'products',
                'order_items.product_id = products.id',
                ['product.name' => 'name']
            )
                ->where(['order_id' => $order->getId()]);

            $items = $this->OrderItemTableGateway->selectWith($select);

            $order->setClient($client);

            foreach ($items as $item) {
                $order->addItem($item);
            }

            $data = $hydrator->extract($order);
            return $data;


        }

        return false;
    }


    /*
    public function find($id)
    {
        $resultSet = $this->TableGateway->select(['id' => (int)$id]);

        if($resultSet->count() == 1){
            $hydrator = new ClassMethods();
            $hydrator->addStrategy('items', new OrderItemHydratorStrategy(new ClassMethods()));

            $order = $resultSet->current();

            $client = $this->clientsTableGateway
                ->select(['id'=>$order->getClientId()])
                ->current();

            $sql = $this->orderItemTableGateway->getSql();
            $select = $sql->select();
            $select->join(
                'products',
                'order_items.product_id = product.id',
                [
                    'product_name'=>'name'
                ]
            )
                ->where(['order_id' => $order->getId()]);

            $items = $this->orderItemTableGateway->selectWith($select);

            $order->setClient($client);
            foreach($items as $item){
                $order->addItem($item);
            }

            $data = $hydrator->extract($order);
            return $data;
        }

        return false;
    }
    */

    public function deleteItem($idOrder)
    {
        $this->OrderItemTableGateway->delete(['order_id'=> $idOrder]);
    }

    public function delete($id)
    {
        $this->TableGateway->delete(['id' => (int)$id]);
        return true;
    }


    public function update (array $data, $id)
    {
        $this->TableGateway->update($data, ['id'=> $id]);

        return $id;
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
            $order->addItem($item);
        }
        $data = $hydrator->extract($order);
        $res[] = $data;

        $arrayAdapter = new ArrayAdapter($res);
        $ordersColletion = new OrdersCollection($arrayAdapter);

        return $ordersColletion;
    }
}
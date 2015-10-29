<?php
/**
 * Created by PhpStorm.
 * User: edimauro
 * Date: 26/10/15
 * Time: 09:26
 */

namespace CodeOrders\V1\Rest\Orders;


use Zend\Stdlib\Hydrator\ObjectProperty;

class OrdersService
{
    /**
     * @var OrdersRepository
     */
    private $repository;

    public function __construct(OrdersRepository $repository)
    {

        $this->repository = $repository;
    }

    /**
     * @param $data
     * @return int
     */
    public function insert($data)
    {
        $hydrator = new ObjectProperty();
        $data = $hydrator->extract($data);

        $orderData = $data;
        unset($orderData['item']);
        $items = $data['item'];

        $tableGateway = $this->repository->getTableGateway();

        try {
            $tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();
            $orderId = $this->repository->insert($orderData);

            foreach ($items as $item) {
                $item['order_id'] = $orderId;
                $this->repository->insertItem($item);
            }
            $tableGateway->getAdapter()->getDriver()->getConnection()->commit();
            return $orderId;
        }catch (\Exception $e){
            $tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
            return 'error';
        }
    }

    public function delete($id)
    {
        $tableGateway = $this->repository->getTableGateway();

        try {
            $tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();

            $order = $tableGateway->select(['id' => (int)$id]);

            if ($order)
            {
                $this->repository->deleteItem($id);
                $deletaOrder = $this->repository->delete($id);
            }
            $tableGateway->getAdapter()->getDriver()->getConnection()->commit();

            return $deletaOrder;

        }catch (\Exception $e){
            $tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
            return 'error';
        }

    }

    public function update($id, $data)
    {
        $hydrator = new ObjectProperty();
        $data = $hydrator->extract($data);

        $orderData = $data;
        unset($orderData['item']);
        $items = $data['item'];

        $tableGateway = $this->repository->getTableGateway();

        try {
            $tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();

            $tableGateway->update($orderData, array('id' => $id));

            $this->repository->deleteItem($id);

            foreach ($items as $item) {
                $item['order_id'] = $id;
                $this->repository->insertItem($item);
            }
            $tableGateway->getAdapter()->getDriver()->getConnection()->commit();
            return $data;
        }catch (\Exception $e){$tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
            return 'error';
        }
    }
}
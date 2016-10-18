<?php
/**
 * Created by PhpStorm.
 * User: edimauro
 * Date: 26/10/15
 * Time: 09:26
 */

namespace CodeOrders\V1\Rest\Orders;


use CodeOrders\V1\Rest\Products\ProductsRepository;
use CodeOrders\V1\Rest\Users\UsersRepository;
use Zend\Stdlib\Hydrator\ObjectProperty;

class OrdersService
{
    /**
     * @var OrdersRepository
     */
    private $repository;
    /**
     * @var UsersRepository
     */
    private $usersRepository;
    /**
     * @var ProductsRepository
     */
    private $productsRepository;

    public function __construct(OrdersRepository $repository, UsersRepository $usersRepository, ProductsRepository $productsRepository)
    {

        $this->repository = $repository;
        $this->usersRepository = $usersRepository;
        $this->productsRepository = $productsRepository;
    }

    /**
     * @param $data
     * @return int
     */
    public function insert($data)
    {
        $hydrator = new ObjectProperty();
<<<<<<< HEAD
=======
        
>>>>>>> c59131adbe8fe963b248788d9c2326e4da539896
        $data->user_id = $this->usersRepository->getAuthenticated()->getId();
        $data->created_at = (new \DateTime())->format('Y-m-d');
        $data->total = 0;
        $items = $data->item;   
        unset($data->item);

        $orderData = $hydrator->extract($data);
        $tableGateway = $this->repository->getTableGateway();

        try {
            $tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();
            $orderId = $this->repository->insert($orderData);

            $total = 0;
<<<<<<< HEAD

            foreach ($items as $key=>$item) {
                $product = $this->productsRepository->find($item['product_id']);
                $item['order_id'] = $orderId;
                $item['price'] = $product['price'];
=======
                
            foreach ($items as $key=>$item) {
                $product = $this->productsRepository->find($item['product_id']);
                $item['order_id'] = $orderId;
                $item['price'] = $product->getPrice();
>>>>>>> c59131adbe8fe963b248788d9c2326e4da539896
                $item['total'] = $items[$key]['total'] = $item['quantity'] * $item['price'];
                $total += $item['total'];

                $this->repository->insertItem($item);
            }

            $this->repository->update(['total'=>$total], $orderId);

            $tableGateway->getAdapter()->getDriver()->getConnection()->commit();
            return ['order_id' => $orderId];
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
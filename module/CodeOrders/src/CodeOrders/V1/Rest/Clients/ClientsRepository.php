<?php
/**
 * Created by PhpStorm.
 * User: edimauro
 * Date: 28/10/15
 * Time: 14:28
 */

namespace CodeOrders\V1\Rest\Clients;

use Zend\Console\Request;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Paginator\Adapter\DbTableGateway;
use Zend\Stdlib\Hydrator\ObjectProperty;
use CodeOrders\V1\Rest\Users\UsersRepository;

class ClientsRepository
{
    /**
     * @var AbstractTableGateway
     */
    private $tableGateway;
    /**
     * @var UsersRepository
     */
    private $usersRepository;

    public function __construct (AbstractTableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        //$this->usersRepository = $usersRepository;
    }

    public function getUsersRepository()
    {
        return $this->usersRepository;
    }

    public function findAll()
    {
        $paginationAdapter = new DbTableGateway($this->tableGateway);

        return new ClientsCollection($paginationAdapter);
    }

    public function find($id)
    {
        $resultset = $this->tableGateway->select(['id' => (int)$id]);

        return $resultset->current();
    }

    public function create ($data)
    {
        $hydrator = new ObjectProperty();
        $data = $hydrator->extract($data);

        return $this->tableGateway->insert($data);
    }

    public function update ($id, $data)
    {
        $hydrator = new ObjectProperty();
        $data = $hydrator->extract($data);

        return $this->tableGateway->update($data, array('id'=> $id));

    }

    public function delete($id)
    {
        $this->tableGateway->delete(['id' => (int)$id]);
        return true;
    }

}
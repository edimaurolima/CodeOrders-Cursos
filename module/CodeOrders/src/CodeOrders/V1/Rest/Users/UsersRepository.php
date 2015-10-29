<?php
/**
 * Created by PhpStorm.
 * User: edimauro
 * Date: 09/10/15
 * Time: 14:08
 */

namespace CodeOrders\V1\Rest\Users;


use Zend\Console\Request;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Paginator\Adapter\DbTableGateway;
use ZendDeveloperTools\Collector\DbCollector;

class UsersRepository
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct (TableGatewayInterface $tableGateway)
    {

        $this->tableGateway = $tableGateway;
    }

    /**
     * @return mixed
     */
    public function findAll()
    {
        $tableGateway = $this->tableGateway;

        $paginatorAdapter = new DbTableGateway($tableGateway);

        return new UsersCollection($paginatorAdapter);
    }

    public function find($id)
    {
        $resultSet = $this->tableGateway->select(['id' => (int)$id]);

        return $resultSet->current();
    }

    public function findByUsername ($username)
    {
        return $this->tableGateway->select(['username' => $username])->current();
    }

    public function create ($data)
    {
        $data = array(
            'username' => $data->username,
            'password' => $data->password,
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'role' => $data->role,
        );

        $this->tableGateway->insert($data);
        $data['id']= $this->tableGateway->getLastInsertValue();

        return $data;
    }

    public function update ($id, $data)
    {
        $data = array(
            'username' => $data->username,
            'password' => $data->password,
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'role' => $data->role,
        );

        $this->tableGateway->update($data, array('id'=> $id));

        return $data;
    }


    public function delete($id)
    {
        $deletar = $this->tableGateway->delete(['id' => (int)$id]);

        return $deletar;
    }
}
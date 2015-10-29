<?php
namespace CodeOrders\V1\Rest\Orders;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;
use CodeOrders\V1\Rest\Users\UsersRepository;

class OrdersResource extends AbstractResourceListener
{

    /**
     * @var OrdersRepository
     */
    private $repository;
    /**
     * @var OrdersService
     */
    private $service;

    public function __construct (OrdersRepository $repository, OrdersService $service)
    {

        $this->repository = $repository;
        $this->service = $service;
    }


    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $userRepository = $this->repository->getUsersRepository();

        $user = $userRepository->findByUsername($this->getIdentity()->getRoleId());

        if ($user->getRole() != 'salesman')
        {
            return new ApiProblem(403, "Desculpe, você não tem permissão para cadastrar pedidos!");
        }

        $result = $this->service->insert($data);
        if ($result =="error"){
            return new ApiProblem(405, 'Erro ao processar Ordem');
        }
    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        return $this->service->delete($id);

    }

    /**
     * Delete a collection, or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function deleteList($data)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for collections');
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function fetch($id)
    {
        $userRepository = $this->repository->getUsersRepository();

        $user = $userRepository->findByUsername($this->getIdentity()->getRoleId());

        if ($user->getRole() == "salesman"){

            return $this->repository->findByIdUsuario($id, $user->getId());
        }

        return $this->repository->find($id);
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array())
    {
        $userRepository = $this->repository->getUsersRepository();

        $user = $userRepository->findByUsername($this->getIdentity()->getRoleId());

        if ($user->getRole() == "salesman"){

            return $this->repository->findAllIdUsuario($user->getId());
        }
        return $this->repository->findAll();
    }

    /**
     * Patch (partial in-place update) a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patch($id, $data)
    {
        return new ApiProblem(405, 'The PATCH method has not been defined for individual resources');
    }

    /**
     * Replace a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function replaceList($data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for collections');
    }

    public function update($id, $data)
    {
        return $this->service->update($id, $data);
    }
}

<?php
namespace CodeOrders\V1\Rest\Clients;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class ClientsResource extends AbstractResourceListener
{
    /**
     * @var ClientsRepository
     */
    private $repository;

    public function __construct (ClientsRepository $repository)
    {

        $this->repository = $repository;
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

        if ($user->getRole() == "admin"){

            return $this->repository->create($data);
        }
        return new ApiProblem(403,'Sem autorização para criar');
    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        $userRepository = $this->repository->getUsersRepository();

        $user = $userRepository->findByUsername($this->getIdentity()->getRoleId());

        if ($user->getRole() == "admin"){

            return $this->repository->delete($id);
        }
        return new ApiProblem(403,'Sem autorização para criar');
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

    /**
     * Update a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($id, $data)
    {
        $userRepository = $this->repository->getUsersRepository();

        $user = $userRepository->findByUsername($this->getIdentity()->getRoleId());

        if ($user->getRole() == "admin"){

            return $this->repository->update($id, $data);
        }
        return new ApiProblem(403,'Sem autorização para Editar');
    }
}

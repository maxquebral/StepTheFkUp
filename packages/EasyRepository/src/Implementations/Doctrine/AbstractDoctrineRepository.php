<?php
declare(strict_types=1);

namespace StepTheFkUp\EasyRepository\Implementations\Doctrine;

use Doctrine\Common\Persistence\ManagerRegistry;
use StepTheFkUp\EasyRepository\Interfaces\ObjectRepositoryInterface;

abstract class AbstractDoctrineRepository implements ObjectRepositoryInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $manager;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;

    /**
     * AbstractDoctrineRepository constructor.
     *
     * @param \Doctrine\Common\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $entityClass = $this->getEntityClass();

        $this->manager = $registry->getManagerForClass($entityClass);
        $this->repository = $this->manager->getRepository($entityClass);
    }

    /**
     * Get entity class managed by the repository.
     *
     * @return string
     */
    abstract protected function getEntityClass(): string;

    /**
     * Get all the objects managed by the repository.
     *
     * @return object[]
     */
    public function all(): array
    {
        return $this->repository->findAll();
    }

    /**
     * Delete given object(s).
     *
     * @param object|object[] $object
     *
     * @return void
     */
    public function delete($object): void
    {
        $this->callManagerMethodForObjects('remove', $object);
    }

    /**
     * Find object for given identifier, return null if not found.
     *
     * @param int|string $identifier
     *
     * @return null|object
     */
    public function find($identifier)
    {
        return $this->repository->find($identifier);
    }

    /**
     * Save given object(s).
     *
     * @param object|object[] $object The object or list of objects to save
     *
     * @return void
     */
    public function save($object): void
    {
        $this->callManagerMethodForObjects('persist', $object);
    }

    /**
     * Call given method on the manager for given object(s).
     *
     * @param string $method
     * @param object|object[] $objects
     *
     * @return void
     */
    private function callManagerMethodForObjects(string $method, $objects): void
    {
        if (\is_array($objects) === false) {
            $objects = [$objects];
        }

        foreach ($objects as $object) {
            $this->manager->$method($object);
        }

        $this->manager->flush();
    }
}
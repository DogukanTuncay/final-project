<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\BadgeServiceInterface;
use App\Interfaces\Repositories\Admin\BadgeRepositoryInterface;

class BadgeService implements BadgeServiceInterface
{
    protected BadgeRepositoryInterface $repository;

    public function __construct(BadgeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Tüm rozetleri al
     */
    public function all()
    {
        return $this->repository->all();
    }

    /**
     * ID ile rozet bul
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Rozet oluştur
     */
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    /**
     * Rozeti güncelle
     */
    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Rozeti sil
     */
    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    /**
     * Rozet durumunu değiştir (aktif/pasif)
     */
    public function toggleStatus(int $id): bool
    {
        return $this->repository->toggleStatus($id);
    }
}
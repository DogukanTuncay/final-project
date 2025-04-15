<?php

namespace App\Interfaces\Repositories\Admin;

use App\Models\FillInTheBlank;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface FillInTheBlankRepositoryInterface
{
    /**
     * Belirtilen ilişkileri eager-load eder
     *
     * @param array|string $relations
     * @return $this
     */
    public function with($relations);

    /**
     * Tüm boşluk doldurma sorularını getirir
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * ID'ye göre boşluk doldurma sorusunu getirir
     *
     * @param int $id
     * @return FillInTheBlank|null
     */
    public function find(int $id): ?FillInTheBlank;

    /**
     * Slug'a göre boşluk doldurma sorusunu getirir
     *
     * @param string $slug
     * @return FillInTheBlank|null
     */
    public function findBySlug(string $slug): ?FillInTheBlank;

    /**
     * Yeni bir boşluk doldurma sorusu oluşturur
     *
     * @param array $data
     * @return FillInTheBlank
     */
    public function create(array $data): FillInTheBlank;

    /**
     * Var olan bir boşluk doldurma sorusunu günceller
     *
     * @param int $id
     * @param array $data
     * @return FillInTheBlank|null
     */
    public function update(int $id, array $data): ?FillInTheBlank;

    /**
     * Bir boşluk doldurma sorusunu siler
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Sayfalama ile boşluk doldurma sorularını getirir
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Belirli bir kritere göre boşluk doldurma sorularını getirir
     *
     * @param array $criteria
     * @return Collection
     */
    public function findWhere(array $criteria): Collection;

    /**
     * Aktif/pasif durumunu değiştirir
     *
     * @param int $id
     * @return bool
     */
    public function toggleStatus(int $id): bool;
} 
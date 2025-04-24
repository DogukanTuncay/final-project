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
     * Slug'a göre boşluk doldurma sorusunu getirir
     *
     * @param string $slug
     * @return FillInTheBlank|null
     */
    public function findBySlug(string $slug): ?FillInTheBlank;

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
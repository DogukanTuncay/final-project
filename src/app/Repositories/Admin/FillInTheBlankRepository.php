<?php

namespace App\Repositories\Admin;

use App\Models\FillInTheBlank;
use App\Interfaces\Repositories\Admin\FillInTheBlankRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class FillInTheBlankRepository implements FillInTheBlankRepositoryInterface
{
    protected $model;
    protected $with = [];

    /**
     * Constructor
     *
     * @param FillInTheBlank $model
     */
    public function __construct(FillInTheBlank $model)
    {
        $this->model = $model;
    }

    /**
     * Belirtilen ilişkileri eager-load eder
     *
     * @param array|string $relations
     * @return $this
     */
    public function with($relations)
    {
        $this->with = is_string($relations) ? func_get_args() : $relations;
        return $this;
    }

    /**
     * Tüm boşluk doldurma sorularını getirir
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->with($this->with)->get();
    }

    /**
     * ID'ye göre boşluk doldurma sorusunu getirir
     *
     * @param int $id
     * @return FillInTheBlank|null
     */
    public function find(int $id): ?FillInTheBlank
    {
        return $this->model->with($this->with)->find($id);
    }

    /**
     * Slug'a göre boşluk doldurma sorusunu getirir
     *
     * @param string $slug
     * @return FillInTheBlank|null
     */
    public function findBySlug(string $slug): ?FillInTheBlank
    {
        return $this->model->with($this->with)->where('slug', $slug)->first();
    }

    /**
     * Yeni bir boşluk doldurma sorusu oluşturur
     *
     * @param array $data
     * @return FillInTheBlank
     */
    public function create(array $data): FillInTheBlank
    {
        return $this->model->create($data);
    }

    /**
     * Var olan bir boşluk doldurma sorusunu günceller
     *
     * @param int $id
     * @param array $data
     * @return FillInTheBlank|null
     */
    public function update(int $id, array $data): ?FillInTheBlank
    {
        $model = $this->find($id);
        if (!$model) {
            return null;
        }

        $model->update($data);
        return $model;
    }

    /**
     * Bir boşluk doldurma sorusunu siler
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $model = $this->find($id);
        if (!$model) {
            return false;
        }

        return $model->delete();
    }

    /**
     * Sayfalama ile boşluk doldurma sorularını getirir
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with($this->with)->paginate($perPage);
    }

    /**
     * Belirli bir kritere göre boşluk doldurma sorularını getirir
     *
     * @param array $criteria
     * @return Collection
     */
    public function findWhere(array $criteria): Collection
    {
        $query = $this->model->with($this->with);

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->get();
    }

    /**
     * Aktif/pasif durumunu değiştirir
     *
     * @param int $id
     * @return bool
     */
    public function toggleStatus(int $id): bool
    {
        $model = $this->find($id);
        if (!$model) {
            return false;
        }

        $model->is_active = !$model->is_active;
        return $model->save();
    }
} 
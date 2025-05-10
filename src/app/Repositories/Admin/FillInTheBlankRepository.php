<?php

namespace App\Repositories\Admin;

use App\Models\FillInTheBlank;
use App\Interfaces\Repositories\Admin\FillInTheBlankRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class FillInTheBlankRepository extends BaseRepository implements FillInTheBlankRepositoryInterface
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
     * Sayfalama ile boşluk doldurma sorularını getirir
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with($this->with)->get();
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
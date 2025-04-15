<?php

namespace App\Interfaces\Repositories\Admin;

use App\Models\TrueFalseQuestion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\Repositories\BaseRepositoryInterface;
interface TrueFalseQuestionRepositoryInterface extends BaseRepositoryInterface
{
   
    /**
     * Sayfalandırma ile Doğru/Yanlış sorularını getir
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Doğru/Yanlış sorusunun aktiflik durumunu değiştir
     *
     * @param int $id
     * @return TrueFalseQuestion|null
     */
    public function toggleStatus(int $id): ?TrueFalseQuestion;
}
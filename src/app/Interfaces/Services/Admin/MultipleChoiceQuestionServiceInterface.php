<?php

namespace App\Interfaces\Services\Admin;

use App\Models\MultipleChoiceQuestion;

interface MultipleChoiceQuestionServiceInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
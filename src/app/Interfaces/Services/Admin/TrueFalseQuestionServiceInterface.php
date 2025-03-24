<?php

namespace App\Interfaces\Services\Admin;

interface TrueFalseQuestionServiceInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
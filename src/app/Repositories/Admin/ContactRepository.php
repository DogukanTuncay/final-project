<?php

namespace App\Repositories\Admin;

use App\Models\Contact;
use App\Interfaces\Repositories\Admin\ContactRepositoryInterface;
use App\Repositories\BaseRepository;

class ContactRepository extends BaseRepository implements ContactRepositoryInterface
{
    public function __construct(Contact $model)
    {
        parent::__construct($model);
    }
}
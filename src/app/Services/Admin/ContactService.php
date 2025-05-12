<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\ContactServiceInterface;
use App\Interfaces\Repositories\Admin\ContactRepositoryInterface;
use App\Services\BaseService;

class ContactService extends BaseService implements ContactServiceInterface
{
    public function __construct(ContactRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
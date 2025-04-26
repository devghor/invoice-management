<?php

declare(strict_types=1);

namespace Modules\User\Repositories;

use Modules\Base\Repositories\Repository;
use Modules\User\Models\User;

final class UserRepository extends Repository
{
    public function model()
    {
        return User::class;
    }
}

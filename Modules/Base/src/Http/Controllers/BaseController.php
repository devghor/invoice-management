<?php

declare(strict_types=1);

namespace Modules\Base\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Base\Traits\HttpResponseTrait;

class BaseController extends Controller
{
    use HttpResponseTrait;

    protected function getPerPage(): int
    {
        return min(request()->get('per_page', config('base.pagination.default')), config('base.pagination.max'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Shelter\Kernel\Http\AbstractController;

class Controller extends AbstractController
{
    //use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('control.auth');
    }

    /**
     * @param Builder|Relation $query
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function paginate($query, int $perPage = 25, int $page = null): LengthAwarePaginator
    {
        $page = $page ?: \request('page');

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage);

        if ($page > 1 && $paginator->isEmpty()) {
            LengthAwarePaginator::currentPageResolver(function () use ($page) {
                return $page - 1;
            });

            $paginator = $query->paginate($perPage);
        }

        return $paginator;
    }

    /**
     * @param LengthAwarePaginator $paginator
     * @param array $extra
     * @param array|null $fields
     * @return array
     */
    public function mapPagination(
        LengthAwarePaginator $paginator,
        array $extra = [],
        array $fields = null
    ): array {
        if ($fields === null) {
            $fields = ['total', 'last_page', 'current_page', 'per_page'];
        }

        $data = Arr::only(
            $paginator->toArray(),
            $fields
        );

        $data['has_more_pages'] = $paginator->hasMorePages();

        return array_merge(
            $data,
            $extra
        );
    }
}

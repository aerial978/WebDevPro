<?php
/*
namespace src\Service;

class PaginationService
{
    public function paginate($model, $search, $page, $limit, $searchMethod, $countMethod)
    {
        $offset = ($page - 1) * $limit;

        if ($search) {
            $items = $model->$searchMethod($search, $offset, $limit);
            $totalItems = $model->$countMethod($search);
        } else {
            $items = $model->$searchMethod($offset, $limit);
            $totalItems = $model->$countMethod();
        }

        $totalPages = ceil($totalItems / $limit);

        return [
            'items' => $items,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ];
    }
}*/

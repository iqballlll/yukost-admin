<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class AppHelpers
{

    public static function badgeIsActive($stat, $msg)
    {
        if ($stat) {
            return ' <span class="badge bg-success">' . $msg . '</span>';
        }
        return '<span class="badge bg-danger">' . $msg . '</span>';
    }

    public static function toDateID($str)
    {
        return Carbon::parse($str)->translatedFormat('d F Y');
    }

    public static function formatToRupiah($amount, $withPrefix = true)
    {
        $formatted = number_format((float) $amount, 0, ',', '.');
        return $withPrefix ? 'Rp. ' . $formatted : $formatted;
    }

    public static function parseRange(?string $range): ?array
    {
        if (!$range)
            return null;

        $parts = explode('|', $range);
        if (count($parts) !== 2)
            return null;

        return [
            Carbon::createFromFormat('Y-m-d', trim($parts[0]))->startOfDay(),
            Carbon::createFromFormat('Y-m-d', trim($parts[1]))->endOfDay(),
        ];
    }


    public static function debugData($data)
    {
        echo "<pre>";
        echo json_encode($data);
        echo "</pre>";
        exit;
    }

    public static function randomColor()
    {
        $colors = [
            '#FFCDD2',
            '#F8BBD0',
            '#E1BEE7',
            '#D1C4E9',
            '#C5CAE9',
            '#BBDEFB',
            '#B3E5FC',
            '#B2EBF2',
            '#B2DFDB',
            '#C8E6C9',
            '#DCEDC8',
            '#F0F4C3',
            '#FFF9C4',
            '#FFECB3',
            '#FFE0B2',
            '#FFCCBC',
            '#D7CCC8',
            '#CFD8DC',
            '#F48FB1',
            '#CE93D8',
            '#9FA8DA',
            '#81D4FA',
            '#80CBC4',
            '#A5D6A7',
            '#E6EE9C',
            '#FFE082',
            '#FFAB91',
            '#BCAAA4',
            '#90A4AE',
            '#A1887F',
            '#DCE775',
            '#4DB6AC',
            '#7986CB',
            '#BA68C8',
            '#FF8A65',
            '#FFD54F',
            '#4FC3F7',
            '#AED581',
            '#FF7043',
            '#26A69A',
            '#5C6BC0',
            '#EC407A',
            '#AB47BC',
            '#FFCA28',
            '#66BB6A',
            '#FF5722',
            '#009688',
            '#3F51B5'
        ];
        return $colors[array_rand($colors)];
    }


    public static function sortIcon($field)
    {
        $currentSort = request('sort');
        $currentOrder = request('order', 'desc');

        if ($currentSort !== $field) {
            return 'bi bi-arrow-down-up';
        }

        return $currentOrder === 'asc' ? 'bi bi-arrow-down' : 'bi bi-arrow-up';
    }

    public static function paginateArray($items, $perPage = 10, $currentPage = null, $options = [])
    {
        $currentPage = $currentPage ?: (LengthAwarePaginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : collect($items);
        $slice = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator($slice, $items->count(), $perPage, $currentPage, $options);
    }


}

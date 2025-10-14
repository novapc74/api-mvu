<?php

namespace App\Service\Api\Product\Features;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Exception\JsonException;

class ProcessedProductCard
{
    private const FIELD_COLORS = 'colors';
    private const FIELD_SIZES = 'sizes';
    private const FIELD_GENDERS = 'genders';

    /**
     * Нормализует массив данных, декодируя JSON-строки в массивы для указанных полей.
     * Изменяет массив по ссылке.
     *
     * @param array &$data Массив данных (изменяется по ссылке).
     * @throws JsonException Если JSON некорректный (опционально, для строгой проверки).
     */
    public static function normalize(array &$data): void
    {
        $fieldsToDecode = ['children', 'properties', 'images'];

        foreach ($fieldsToDecode as $field) {
            if (array_key_exists($field, $data) && is_string($data[$field])) {
                $decoded = json_decode($data[$field], true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $data[$field] = $decoded;
                    continue;
                }

                $errorMessage = sprintf('Не обрабтали поле: %s, ошибка: %s.', $field, json_last_error_msg());
                throw new JsonException($errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    private static function buildModifications(array &$data): void
    {
        $modifications = [];
        foreach ($data['children'] ?? [] as $child) {

            $stockData = self::getMinPriceStock($child['stocks'] ?? []);
            $gender = $child['gender']['name'] ?? null;
            $size = $child['size']['name'] ?? null;
            $color = $child['color']['hex'] ?? null;

            /** отсутствие какого-либо поля недопустимо, пропускаем */
            if (!$stockData || !$gender || !$size || !$color) {
                continue;
            }

            $modifications[self::FIELD_COLORS][$color][$size][$gender] = $stockData;
            $modifications[self::FIELD_SIZES][$size][$color][$gender] = $stockData;
            $modifications[self::FIELD_GENDERS][$gender][$color][$size] = $stockData;
        }

        $data['children'] = $modifications;
    }

    private static function getMinPriceStock(array $stocks): ?array
    {
        $needleStock = $minPrice = null;
        foreach ($stocks as $stock) {

            if (empty($stock['price']) || !is_numeric($stock['price'])) {
                continue;
            }

            if ($minPrice === null) {
                $minPrice = $stock['price'];
                $needleStock = $stock;
            }

            if ($minPrice > $stock['price']) {
                $minPrice = $stock['price'];
                $needleStock = $stock;
            }
        }

        return $minPrice ? $needleStock : null;
    }

    private static function buildFiltersForTwig(array &$data): void
    {
        $allSizes = [];
        $allGenders = [];

        if (isset($data['children']['colors'])) {
            foreach ($data['children']['colors'] as $sizesObj) {
                foreach ($sizesObj as $size => $gendersObj) {

                    if (!in_array($size, $allSizes)) {
                        $allSizes[] = $size;
                    }

                    if (is_array($gendersObj)) {
                        foreach ($gendersObj as $gender => $stock) {
                            if (!in_array($gender, $allGenders)) {
                                $allGenders[] = $gender;
                            }
                        }
                    }
                }
            }
        }

        $data['all_sizes'] = $allSizes;
        $data['all_genders'] = $allGenders;
        $data['object'] = json_encode($data['children'], JSON_UNESCAPED_UNICODE);
    }

    public static function processed(array &$data): void
    {
        /** конвертируем json в массив */
        self::normalize($data);

        /** строим структуру для twig */
        self::buildModifications($data);

        /** собираем данные для фильтров в twig */
        self::buildFiltersForTwig($data);
    }
}

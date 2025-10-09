<?php

namespace App\Service\Api\Product;

use App\Exception\CustomException;
use App\Repository\ProductRepository;
use App\Repository\SizeRepository;
use App\Service\Api\Product\Dto\ProductPageDto;

readonly class ApiProductService
{
    public function __construct(
        private ProductRepository $productRepository,
        private SizeRepository    $sizeRepository,
    )
    {
    }

    /**
     * @throws CustomException
     */
    public function getProductData(string $slug): array
    {
        $dto = new ProductPageDto($slug);

        $data = $this->productRepository->getProductPageData($dto);

        return self::processProductData($data);

    }

    private function processProductData(array $data): array
    {
        $variants = $data['children'];
        $grouped = [];

        // $sizeOrder = ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];
        $sizeOrder = $this->sizeRepository->getSizeNameCollection();

        foreach ($variants as $variant) {
            $gender = $variant['gender'];
            $color = $variant['color']['color'];
            $size = $variant['size'];

            // Вычислить минимальную цену и общий запас
            $minPrice = null;
            $totalAmount = 0;
            $warehouses = [];
            foreach ($variant['stock'] as $stock) {
                $priceValue = (float)str_replace([' руб.', ' '], '', $stock['price']);
                if ($minPrice === null || $priceValue < $minPrice) {
                    $minPrice = $priceValue;
                }
                $totalAmount += $stock['amount'];
                $warehouses[] = $stock['warehouse']['name'] . ' (' . $stock['price'] . ')';
            }

            // Группировка
            if (!isset($grouped[$gender])) {
                $grouped[$gender] = [];
            }

            if (!isset($grouped[$gender][$color])) {
                $grouped[$gender][$color] = [
                    'hex' => $variant['color']['hex_code'],
                    'sizes' => []
                ];
            }

            $grouped[$gender][$color]['sizes'][$size] = [
                'variant_id' => $variant['variant_id'],
                'popularity' => $variant['popularity_index'],
                'min_price' => $minPrice,
                'total_amount' => $totalAmount,
                'warehouses' => $warehouses,
                'available' => $totalAmount > 0
            ];
        }

        // Сортировка: гендеры по алфавиту, цвета по популярности (средний popularity), размеры по порядку
        ksort($grouped);

        foreach ($grouped as &$colors) {

            uasort($colors, function ($a, $b) {
                $aPop = array_sum(array_column($a['sizes'], 'popularity')) / count($a['sizes']);
                $bPop = array_sum(array_column($b['sizes'], 'popularity')) / count($b['sizes']);
                return $bPop <=> $aPop; // По убыванию популярности
            });

            foreach ($colors as &$colorData) {

                uksort($colorData['sizes'], function ($a, $b) use ($sizeOrder) {
                    return array_search($a, $sizeOrder) <=> array_search($b, $sizeOrder);
                });

            }
        }

        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'images' => $data['images'],
            'properties' => $data['properties'],
            'grouped_variants' => $grouped,
        ];
    }

}

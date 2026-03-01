<?php

namespace App\Services\Schema;

use Config\AppConfig;

class ProductSchemaBuilder
{
    public function __construct(private OfferSchemaBuilder $offerBuilder) {}

    public function build(object $product, int $index): array
    {
        $appUrl = AppConfig::getConst('URL_PATH');

        return [
            "@type" => "ListItem",
            "position" => $index + 1,
            "item" => [
                "@type" => "Product",
                "url" => $appUrl . "product/detail/" . $product->slug,
                "name" => trim($product->name),
                "image" => isset($product->images[0])
                    ? $appUrl . "build/img/products/" . $product->images[0]->filePath
                    : null,
                "description" => $product->description ?? "Description du produit " . $product->name,
                "category" => $product->categories[0]->name ?? null,
                "brand" => [
                    "@type" => "Brand",
                    "name" => $product->brand ?? "Generic Brand"
                ],
                "aggregateRating" => [
                    "@type" => "AggregateRating",
                    "ratingValue" => "4.8",
                    "reviewCount" => "89"
                ],
                "offers" => $this->offerBuilder->build($product->price ?? 150.00)
            ]
        ];
    }
}

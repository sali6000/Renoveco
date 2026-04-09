<?php

namespace Src\Services\Schema;

class SchemaBuilder
{
    public function __construct(private ProductSchemaBuilder $productBuilder) {}

    public function buildProductList(array $products): string
    {
        $data = [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "itemListElement" => array_map(
                fn($product, $i) => $this->productBuilder->build($product, $i),
                $products,
                array_keys($products)
            )
        ];

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

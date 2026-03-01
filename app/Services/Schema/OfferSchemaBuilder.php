<?php

namespace App\Services\Schema;

class OfferSchemaBuilder
{
    public function __construct(
        private ShippingSchemaBuilder $shippingBuilder,
        private ReturnPolicySchemaBuilder $returnBuilder
    ) {}

    public function build(float $price, string $availability = 'https://schema.org/InStock'): array
    {
        return [
            "@type" => "Offer",
            "priceCurrency" => "EUR",
            "price" => number_format($price, 2, '.', ''),
            "priceValidUntil" => date('Y-m-d', strtotime('+1 year')),
            "availability" => $availability,
            "itemCondition" => "https://schema.org/NewCondition",
            "shippingDetails" => $this->shippingBuilder->build(),
            "hasMerchantReturnPolicy" => $this->returnBuilder->build()
        ];
    }
}

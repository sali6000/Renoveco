<?php

namespace Src\Services\Schema;

class ShippingSchemaBuilder
{
    public function build(): array
    {
        return [
            "@type" => "OfferShippingDetails",
            "shippingRate" => [
                "@type" => "MonetaryAmount",
                "value" => 10.00,
                "currency" => "EUR"
            ],
            "shippingDestination" => [
                "@type" => "DefinedRegion",
                "addressCountry" => "BE"
            ],
            "deliveryTime" => [
                "@type" => "ShippingDeliveryTime",
                "handlingTime" => [
                    "@type" => "QuantitativeValue",
                    "minValue" => 1,
                    "maxValue" => 2,
                    "unitCode" => "d"
                ],
                "transitTime" => [
                    "@type" => "QuantitativeValue",
                    "minValue" => 1,
                    "maxValue" => 3,
                    "unitCode" => "d"
                ]
            ]
        ];
    }
}

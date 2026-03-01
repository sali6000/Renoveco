<?php

namespace App\Services\Schema;

class SchemaFactory
{
    public static function createProductSchema(): SchemaBuilder
    {
        $returnPolicy = new ReturnPolicySchemaBuilder();
        $shippingPolicy = new ShippingSchemaBuilder();
        $offerPolicy = new OfferSchemaBuilder($shippingPolicy, $returnPolicy);
        $productSchema = new ProductSchemaBuilder($offerPolicy);

        return new SchemaBuilder($productSchema);
    }
}

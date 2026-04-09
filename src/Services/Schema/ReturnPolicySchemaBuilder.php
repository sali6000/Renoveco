<?php

namespace Src\Services\Schema;

class ReturnPolicySchemaBuilder
{
    public function build(string $country = 'BE'): array
    {
        return [
            "@type" => "MerchantReturnPolicy",
            "applicableCountry" => $country,
            "returnPolicyCategory" => "https://schema.org/ReturnPolicyCategoryFreeReturn",
            "returnPolicyCountry" => $country,
            "returnFees" => "https://schema.org/FreeReturn"
        ];
    }
}

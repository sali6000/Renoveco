<?php

namespace Src\Database;

use Core\Database\Relations\ManyToManyRelation;
use Core\Database\Relations\ManyToOneRelation;
use Core\Database\Relations\OneToManyRelation;

final class SchemaMysql
{
    // -------------------------------------------------------
    // 🧩 ATTRIBUTE
    // -------------------------------------------------------    
    public const TABLE_ATTRIBUTES = 'attributes att';
    public const ATTRIBUTES_ID = 'att.id';
    public const ATTRIBUTES_DOMAIN_ID = 'att.domain_id';
    public const ATTRIBUTES_NAME = 'att.name';
    public const ATTRIBUTES_TYPE = 'att.type';
    public const ATTRIBUTES_UNIT = 'att.unit';
    public const ATTRIBUTES_IS_REQUIRED = 'att.is_required';
    public const ATTRIBUTES_PARENT_ATTRIBUTE_ID = 'att.parent_attribute_id';
    public const ATTRIBUTES_ATTRIBUTE_GROUP_ID = 'att.attribute_group_id';
    public const ATTRIBUTES_RELATION_PREFIX = 'attribute_';

    // -------------------------------------------------------
    // 🧩 ATTRIBUTE : GROUPS
    // -------------------------------------------------------
    public const TABLE_ATTRIBUTE_GROUPS = 'attribute_groups attgro';
    public const ATTRIBUTE_GROUPS_ID = 'attgro.id';
    public const ATTRIBUTE_GROUPS_DOMAIN_ID = 'attgro.domain_id';
    public const ATTRIBUTE_GROUPS_NAME = 'attgro.name';
    public const ATTRIBUTE_GROUPS_DISPLAY_ORDER = 'attgro.display_order';

    // -------------------------------------------------------
    // 🧩 PRODUCT : ATTRIBUTE
    // -------------------------------------------------------
    public const TABLE_PRODUCT_ATTRIBUTE = 'product_attribute proatt';
    public const PRODUCT_ATTRIBUTE_ID = 'proatt.id';
    public const PRODUCT_ATTRIBUTE_PRODUCT_ID = 'proatt.product_id';
    public const PRODUCT_ATTRIBUTE_ATTRIBUTE_ID = 'proatt.attribute_id';
    public const PRODUCT_ATTRIBUTE_VALUE = 'proatt.value';
    public const PRODUCT_ATTRIBUTE_CREATED_AT = 'proatt.created_at';
    public const PRODUCT_ATTRIBUTE_UPDATED_AT = 'proatt.updated_at';

    public static function productAttributesRelation(array $columns): ManyToManyRelation
    {
        return new ManyToManyRelation(
            // SET KEY ARRAY (ex: User['roles'][...])
            key: self::fieldTable(self::TABLE_ATTRIBUTES),

            // SET COLUMNS TO GET (ex: self::ROLE_COLUMNS_MINIMAL)
            relationColumns: $columns,

            // SET PREFIX FOR ROLE COLUMNS
            relationPrefix: self::ATTRIBUTES_RELATION_PREFIX,

            // PARAMS SPECIFIQUE TARGET TABLE
            relatedTable: self::TABLE_ATTRIBUTES,
            foreignKey: self::ATTRIBUTES_ID,
            localKey: self::PRODUCT_ID,

            // PARAMS SPECIFIQUE PIVOT (ManyToManyRelation)
            pivotTable: self::TABLE_PRODUCT_ATTRIBUTE,
            pivotForeignKey: self::PRODUCT_ATTRIBUTE_ATTRIBUTE_ID,
            pivotLocalKey: self::PRODUCT_ATTRIBUTE_PRODUCT_ID,
        );
    }


    // -------------------------------------------------------
    // 🧩 CATEGORY : PRODUCT
    // -------------------------------------------------------
    public const TABLE_CATEGORY_PRODUCT = 'category_product catpro';
    public const CATEGORY_PRODUCT_PRODUCT_ID = 'catpro.product_id';
    public const CATEGORY_PRODUCT_CATEGORY_ID = 'catpro.category_id';

    // -------------------------------------------------------
    // 🧩 ROLE : USER
    // -------------------------------------------------------
    public const TABLE_ROLE_USER = 'role_user roluse';
    public const ROLE_USER_USER_ID = 'roluse.user_id';
    public const ROLE_USER_ROLE_ID = 'roluse.role_id';

    // -------------------------------------------------------
    // 🧩 PRODUCT
    // -------------------------------------------------------
    public const TABLE_PRODUCTS = 'products pro';
    public const PRODUCT_ID = 'pro.id';
    public const PRODUCT_REFERENCE = 'pro.reference';
    public const PRODUCT_SLUG = 'pro.slug';
    public const PRODUCT_NAME = 'pro.name';
    public const PRODUCT_DESCRIPTION = 'pro.description';
    public const PRODUCT_COMPOSITION = 'pro.composition';
    public const PRODUCT_USE_FOR = 'pro.use_for';
    public const PRODUCT_IS_ACTIVE = 'pro.is_active';
    public const PRODUCT_DEFAULT_SUPPLIER_ID = 'pro.default_supplier_id';
    public const PRODUCT_CREATED_AT = 'pro.created_at';
    public const PRODUCT_UPDATED_AT = 'pro.updated_at';
    public const PRODUCT_SUBTITLE = 'pro.subtitle';
    public const PRODUCT_META_DESCRIPTION = 'pro.meta_description';
    public const PRODUCT_FEATURES = 'pro.features';

    // -------------------------------------------------------
    // 🧩 STOCK : PRODUCT
    // -------------------------------------------------------
    public const TABLE_STOCK_PRODUCT = 'stock_product stopro';
    public const STOCK_PRODUCT_ID = 'stopro.id';
    public const STOCK_PRODUCT_PRODUCT_ID = 'stopro.product_id';
    public const STOCK_PRODUCT_QUANTITY = 'stopro.quantity';
    public const STOCK_PRODUCT_STOCK_MINIMUM = 'stopro.stock_minimum';
    public const STOCK_PRODUCT_STOCK_MAXIMUM = 'stopro.stock_maximum';
    public const STOCK_PRODUCT_RELATION_PREFIX = 'stopro_';

    public static function productStockRelation(array $columns): ManyToOneRelation
    {
        return new ManyToOneRelation(
            key: self::fieldTable(self::TABLE_STOCK_PRODUCT),
            relationColumns: $columns,
            relationPrefix: self::STOCK_PRODUCT_RELATION_PREFIX,

            relatedTable: self::TABLE_STOCK_PRODUCT,
            localKey: self::PRODUCT_ID,
            foreignKey: self::STOCK_PRODUCT_PRODUCT_ID,
        );
    }

    // -------------------------------------------------------
    // 🧩 STOCK : LOCATION
    // -------------------------------------------------------
    public const TABLE_STOCK_LOCATION = 'stock_location stoloc';
    public const STOCK_LOCATION_ID = 'stoloc.id';
    public const STOCK_LOCATION_NAME = 'stoloc.name';
    public const STOCK_LOCATION_DESCRIPTION = 'stoloc.description';


    // -------------------------------------------------------
    // 🧩 STOCK : PRODUCT : LOCATION
    // -------------------------------------------------------
    public const TABLE_STOCK_PRODUCT_LOCATION = 'stock_product_location stoproloc';
    public const STOCK_PRODUCT_LOCATION_ID = 'stoproloc.id';
    public const STOCK_PRODUCT_LOCATION_PRODUCT_STOCK_ID = 'stoproloc.product_stock_id';
    public const STOCK_PRODUCT_LOCATION_STOCK_LOCATION_ID = 'stoproloc.stock_location_id';
    public const STOCK_PRODUCT_LOCATION_QUANTITY = 'stoproloc.quantity';
    public const STOCK_PRODUCT_LOCATION_CREATED_AT = 'stoproloc.created_at';
    public const STOCK_PRODUCT_LOCATION_UPDATED_AT = 'stoproloc.updated_at';

    // -------------------------------------------------------
    // 🧩 CATÉGORIES
    // -------------------------------------------------------
    public const TABLE_CATEGORIES = 'categories cat';
    public const CATEGORY_ID = 'cat.id';
    public const CATEGORY_SLUG = 'cat.slug';
    public const CATEGORY_NAME = 'cat.name';
    public const CATEGORY_DESCRIPTION = 'cat.description';
    public const CATEGORY_PARENT_ID = 'cat.parent_id';
    public const CATEGORY_RELATION_PREFIX = "cat_";


    public static function productCategoriesRelation(array $columns): ManyToManyRelation
    {
        return new ManyToManyRelation(
            // SET KEY ARRAY (ex: User['roles'][...])
            key: self::fieldTable(self::TABLE_CATEGORIES),

            // SET COLUMNS TO GET (ex: self::ROLE_COLUMNS_MINIMAL)
            relationColumns: $columns,

            // SET PREFIX FOR ROLE COLUMNS
            relationPrefix: self::CATEGORY_RELATION_PREFIX,

            // PARAMS SPECIFIQUE TARGET TABLE
            relatedTable: self::TABLE_CATEGORIES,
            foreignKey: self::CATEGORY_ID,
            localKey: self::PRODUCT_ID,

            // PARAMS SPECIFIQUE PIVOT (ManyToManyRelation)
            pivotTable: self::TABLE_CATEGORY_PRODUCT,
            pivotForeignKey: self::CATEGORY_PRODUCT_CATEGORY_ID,
            pivotLocalKey: self::CATEGORY_PRODUCT_PRODUCT_ID,
        );
    }

    // -------------------------------------------------------
    // 🧩 PRODUIT : IMAGE
    // -------------------------------------------------------
    public const TABLE_PRODUCT_IMAGES = 'product_images proima';
    public const PRODUCT_IMAGE_ID = 'proima.id';
    public const PRODUCT_IMAGE_PRODUCT_ID = 'proima.product_id';
    public const PRODUCT_IMAGE_FILE_PATH = 'proima.file_path';
    public const PRODUCT_IMAGE_ALT_TEXT = 'proima.alt_text';
    public const PRODUCT_IMAGE_IS_MAIN = 'proima.is_main';
    public const PRODUCT_IMAGE_RELATION_PREFIX = 'image_';

    public static function productImagesRelation(array $columns): OneToManyRelation
    {
        return new OneToManyRelation(
            key: self::fieldTable(self::TABLE_PRODUCT_IMAGES),
            relationColumns: $columns,
            relationPrefix: self::PRODUCT_IMAGE_RELATION_PREFIX,

            // JOIN PARAMS
            relatedTable: self::TABLE_PRODUCT_IMAGES,
            localKey: self::PRODUCT_ID,
            foreignKey: self::PRODUCT_IMAGE_PRODUCT_ID,
        );
    }

    // -------------------------------------------------------
    // 🧩 ROLE
    // -------------------------------------------------------
    public const TABLE_ROLES = 'roles rol';
    public const ROLE_ID = 'rol.id';
    public const ROLE_NAME = 'rol.name';
    public const ROLE_IS_ACTIVE = 'rol.is_active';
    private const ROLE_RELATION_PREFIX = 'role_';


    // -------------------------------------------------------
    // 🧩 USER
    // -------------------------------------------------------
    public const TABLE_USERS = 'users usr';
    public const USER_ALL = 'usr.*';
    public const USER_ID = 'usr.id';
    public const USER_EMAIL = 'usr.email';
    public const USER_PASSWORD_HASH = 'usr.password_hash';
    public const USER_CREATED_AT = 'usr.created_at';
    public const USER_LAST_LOGIN_AT = 'usr.last_login_at';
    public const USER_EMAIL_VERIFIED_AT = 'usr.email_verified_at';
    public const USER_DELETED_AT = 'usr.deleted_at';
    public const USER_IS_ACTIVE = 'usr.is_active';

    public static function userRolesRelation(array $columns): ManyToManyRelation
    {
        return new ManyToManyRelation(
            // SET KEY ARRAY (ex: User['roles'][...])
            key: self::fieldTable(self::TABLE_ROLES),

            // SET COLUMNS TO GET (ex: self::ROLE_COLUMNS_MINIMAL)
            relationColumns: $columns,

            // SET PREFIX FOR ROLE COLUMNS
            relationPrefix: self::ROLE_RELATION_PREFIX,

            // PARAMS SPECIFIQUE TARGET TABLE
            relatedTable: self::TABLE_ROLES,
            foreignKey: self::ROLE_ID,
            localKey: self::USER_ID,

            // PARAMS SPECIFIQUE PIVOT (ManyToManyRelation)
            pivotTable: self::TABLE_ROLE_USER,
            pivotLocalKey: self::ROLE_USER_USER_ID,
            pivotForeignKey: self::ROLE_USER_ROLE_ID,
        );
    }

    // -------------------------------------------------------
    // 🧩 RATE LIMIT ATTEMPTS (📋 Limite de tentatives)
    // -------------------------------------------------------
    public const TABLE_RATE_LIMIT_ATTEMPTS = 'rate_limit_attempts rla';
    public const RATE_LIMIT_ID         = 'rla.id';
    public const RATE_LIMIT_TYPE       = 'rla.type';
    public const RATE_LIMIT_IP         = 'rla.ip_address';
    public const RATE_LIMIT_IDENTIFIER = 'rla.identifier';
    public const RATE_LIMIT_AT         = 'rla.attempted_at';

    // -------------------------------------------------------
    // 🧩 SUPPLIER (Fournisseeurs)
    // -------------------------------------------------------
    public const TABLE_SUPPLIERS = 'suppliers sup';
    public const SUPPLIER_ID = 'sup.id';
    public const SUPPLIER_NAME = 'sup.name';

    // ==========================================================================================
    // ⚙️ Méthodes utilitaires =>
    // ==========================================================================================
    /**
     * Retourne la propriété sans préfixe ex: "rol.name" devient "name"
     */
    public static function fieldProperty(string $fieldScheme): string
    {
        $parts = explode('.', $fieldScheme);
        return end($parts); // retourne le dernier segment
    }

    /**
     * Retourne la table sans préfixe ex: "role rol" devient "role"
     */
    public static function fieldTable(string $tableScheme): string
    {
        return preg_split('/\s+/', trim($tableScheme))[0];
    }
}

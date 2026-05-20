<?php

namespace Src\Database;

final class SchemaMysql
{
    // ==========================================================================================
    // 🧱 CONSTRUCTION =>
    // ==========================================================================================
    public const TABLE_ATTRIBUTES = 'attributes att';
    public const ATTRIBUTES_ID = 'att.id';
    public const ATTRIBUTES_DOMAIN_ID = 'att.domain_id';
    public const ATTRIBUTES_NAME = 'att.name';
    public const ATTRIBUTES_TYPE = 'att.type';
    public const ATTRIBUTES_UNIT = 'att.unit';
    public const ATTRIBUTES_IS_REQUIRED = 'att.is_required';
    public const ATTRIBUTES_PARENT_ATTRIBUTE_ID = 'att.parent_attribute_id';
    public const ATTRIBUTES_ATTRIBUTE_GROUP_ID = 'att.attribute_group_id';

    public const TABLE_ATTRIBUTE_GROUPS = 'attribute_groups attgro';
    public const ATTRIBUTE_GROUPS_ID = 'attgro.id';
    public const ATTRIBUTE_GROUPS_DOMAIN_ID = 'attgro.domain_id';
    public const ATTRIBUTE_GROUPS_NAME = 'attgro.name';
    public const ATTRIBUTE_GROUPS_DISPLAY_ORDER = 'attgro.display_order';

    public const TABLE_PRODUCT_ATTRIBUTE = 'product_attribute proatt';
    public const PRODUCT_ATTRIBUTE_ID = 'proatt.id';
    public const PRODUCT_ATTRIBUTE_PRODUCT_ID = 'proatt.product_id';
    public const PRODUCT_ATTRIBUTE_ATTRIBUTE_ID = 'proatt.attribute_id';
    public const PRODUCT_ATTRIBUTE_VALUE = 'proatt.value';
    public const PRODUCT_ATTRIBUTE_CREATED_AT = 'proatt.created_at';
    public const PRODUCT_ATTRIBUTE_UPDATED_AT = 'proatt.updated_at';


    // ==========================================================================================
    // 🧱 ENREGISTREMENT =>
    // ==========================================================================================
    // ==========================================================================================
    // 🧱 TABLES =>
    // ==========================================================================================
    public const TABLE_CATEGORIES = 'categories c';
    public const TABLE_PRODUCTS = 'products p';
    public const TABLE_ROLES = 'roles r';
    public const TABLE_SUPPLIERS = 'suppliers s';
    public const TABLE_USERS = 'users u';
    public const TABLE_RATE_LIMIT_ATTEMPTS = 'rate_limit_attempts rla'; // 📋 Limite de tentatives
    public const TABLE_PRODUCT_INVENTORY = 'product_inventory proinv';


    // ==========================================================================================
    // 🔗 RELATIONS =>
    // ==========================================================================================

    // -------------------------------------------------------
    // 🔗 OneToMany (1 <- N) Ex: Product <- Images
    // -------------------------------------------------------
    public const TABLE_PRODUCT_IMAGES = 'product_images pi'; // // Produit <- Images

    // -------------------------------------------------------
    // 🔗 ManyToMany (N <-> N) Ex: Products <-> Catégories
    // -------------------------------------------------------
    public const TABLE_PIVOT_CATEGORY_PRODUCT = 'category_product cp'; // Produits <-> Catégories
    public const PIVOT_CATEGORY_PRODUCT_FK_PRODUCT = 'cp.product_id';
    public const PIVOT_CATEGORY_PRODUCT_FK_CATEGORY = 'cp.category_id';

    public const TABLE_PIVOT_ROLE_USER = 'role_user ru'; // Roles <-> Utilisateurs
    public const PIVOT_ROLE_USER_FK_USER = 'ru.user_id';
    public const PIVOT_ROLE_USER_FK_ROLE = 'ru.role_id';

    // ==========================================================================================
    // 🧩 PROPRIÉTÉS =>
    // ==========================================================================================
    // -------------------------------------------------------
    // 🧩 PRODUIT
    // -------------------------------------------------------
    public const PRODUCT_ID = 'p.id';
    public const PRODUCT_REFERENCE = 'p.reference';
    public const PRODUCT_SLUG = 'p.slug';
    public const PRODUCT_NAME = 'p.name';
    public const PRODUCT_DESCRIPTION = 'p.description';
    public const PRODUCT_COMPOSITION = 'p.composition';
    public const PRODUCT_USE_FOR = 'p.use_for';
    public const PRODUCT_IS_ACTIVE = 'p.is_active';
    public const PRODUCT_DEFAULT_SUPPLIER_ID = 'p.default_supplier_id';
    public const PRODUCT_CREATED_AT = 'p.created_at';
    public const PRODUCT_UPDATED_AT = 'p.updated_at';
    public const PRODUCT_SUBTITLE = 'p.subtitle';
    public const PRODUCT_META_DESCRIPTION = 'p.meta_description';
    public const PRODUCT_FEATURES = 'p.features';

    // -------------------------------------------------------
    // 🧩 PRODUIT : STOCK
    // -------------------------------------------------------
    public const PRODUCT_INVENTORY_ID = 'proinv.id';
    public const PRODUCT_INVENTORY_PRODUCT_ID = 'proinv.product_id';
    public const PRODUCT_INVENTORY_STOCK_QUANTITY = 'proinv.stock_quantity';
    public const PRODUCT_INVENTORY_STOCK_MINIMUM = 'proinv.stock_minimum';
    public const PRODUCT_INVENTORY_STOCK_MAXIMUM = 'proinv.stock_maximum';
    public const PRODUCT_INVENTORY_LAST_STOCK_UPDATE = 'proinv.last_stock_update';

    // -------------------------------------------------------
    // 🧩 PRODUIT : CATÉGORIES
    // -------------------------------------------------------
    public const CATEGORY_ID = 'c.id';
    public const CATEGORY_SLUG = 'c.slug';
    public const CATEGORY_NAME = 'c.name';
    public const CATEGORY_DESCRIPTION = 'c.description';
    public const CATEGORY_PARENT_ID = 'c.parent_id';

    // -------------------------------------------------------
    // 🧩 PRODUIT : IMAGE
    // -------------------------------------------------------
    public const PRODUCT_IMAGE_ID = 'pi.id';
    public const PRODUCT_IMAGE_PRODUCT_ID = 'pi.product_id';
    public const PRODUCT_IMAGE_FILE_PATH = 'pi.file_path';
    public const PRODUCT_IMAGE_ALT_TEXT = 'pi.alt_text';
    public const PRODUCT_IMAGE_IS_MAIN = 'pi.is_main';

    // -------------------------------------------------------
    // 🧩 ROLE
    // -------------------------------------------------------
    public const ROLE_ID = 'r.id';
    public const ROLE_NAME = 'r.name';

    // -------------------------------------------------------
    // 🧩 USER
    // -------------------------------------------------------
    public const USER_ALL = 'u.*';
    public const USER_ID = 'u.id';
    public const USER_EMAIL = 'u.email';
    public const USER_PASSWORD_HASH = 'u.password_hash';
    public const USER_CREATED_AT = 'u.created_at';
    public const USER_LAST_LOGIN_AT = 'u.last_login_at';
    public const USER_EMAIL_VERIFIED_AT = 'u.email_verified_at';
    public const USER_DELETED_AT = 'u.deleted_at';
    public const USER_IS_ACTIVE = 'u.is_active';

    // -------------------------------------------------------
    // 🧩 RATE LIMIT ATTEMPTS
    // -------------------------------------------------------
    public const RATE_LIMIT_ID         = 'rla.id';
    public const RATE_LIMIT_TYPE       = 'rla.type';
    public const RATE_LIMIT_IP         = 'rla.ip_address';
    public const RATE_LIMIT_IDENTIFIER = 'rla.identifier';
    public const RATE_LIMIT_AT         = 'rla.attempted_at';

    // -------------------------------------------------------
    // 🧩 FOURNISSEURS
    // -------------------------------------------------------
    public const SUPPLIER_ID = 's.id';
    public const SUPPLIER_NAME = 's.name';

    // ==========================================================================================
    // ⚙️ Méthodes utilitaires =>
    // ==========================================================================================
    /**
     * Retourne la propriété (sans le point ni l'alias)
     */
    public static function fieldProperty(string $fieldScheme): string
    {
        $parts = explode('.', $fieldScheme);
        return end($parts); // retourne le dernier segment
    }

    /**
     * Retourne la table (sans l'espace ni l'alias)
     */
    public static function fieldTable(string $tableScheme): string
    {
        return preg_split('/\s+/', trim($tableScheme))[0];
    }
}

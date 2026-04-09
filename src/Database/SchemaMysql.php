<?php

namespace Src\Database;

final class SchemaMysql
{
    // ==========================================================================================
    // 🧱 TABLES =>
    // ==========================================================================================
    public const TABLE_CATEGORIES = 'categories c';
    public const TABLE_PRODUCTS = 'products p';
    public const TABLE_ROLES = 'roles r';
    public const TABLE_SUPPLIERS = 'suppliers s';
    public const TABLE_USERS = 'users u';

    // ==========================================================================================
    // 🔗 TABLES RELATIONNELLES =>
    // ==========================================================================================
    // -------------------------------------------------------
    // 🔗 OneToMany (1 <- N) Ex: Product <- Images
    // -------------------------------------------------------
    public const TABLE_PRODUCT_IMAGES = 'product_images pi'; // // Produit <- Images
    public const TABLE_PRODUCT_INVENTORY = 'product_inventory pi';

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

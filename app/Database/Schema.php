<?php

namespace App\Database;

/**
 * Classe de définition centralisée des noms de tables et colonnes SQL.
 *
 * ➜ Avantages :
 * - Évite les fautes de frappe dans les requêtes SQL.
 * - Permet de renommer facilement une colonne ou table.
 * - Fournit un schéma clair pour tout le projet.
 */
final class Schema
{
    // ==============================
    // 🧱 TABLES => OneToOne (1 <- 1) Ex: Product <- Supplier
    // ==============================
    public const TABLE_CATEGORIES = 'categories c';
    public const TABLE_PRODUCTS = 'products p';
    public const TABLE_ROLES = 'roles r';
    public const TABLE_SUPPLIERS = 'suppliers s';
    public const TABLE_USERS = 'users u';

    // ==============================
    // 🔗 TABLES => OneToMany (1 <- *) Ex: Product <- Images
    // ==============================
    public const TABLE_PRODUCT_IMAGES = 'product_images pi';

    // ==============================
    // 🔗 TABLES => ManyToMany (* <-> *) Ex: Products <-> Catégories
    // ==============================
    public const TABLE_PIVOT_CATEGORY_PRODUCT = 'category_product cp';
    public const TABLE_PIVOT_ROLE_USER = 'role_user ru';


    // ==============================
    // 🧩 PRODUIT
    // ==============================
    public const PRODUCT_ID = 'p.id';
    public const PRODUCT_NAME = 'p.name';
    public const PRODUCT_REFERENCE = 'p.reference';
    public const PRODUCT_SLUG = 'p.slug';
    public const PRODUCT_DESCRIPTION = 'p.description';
    public const PRODUCT_COMPOSITION = 'p.composition';
    public const PRODUCT_IS_ACTIVE = 'p.is_active';

    // ==============================
    // 🏷️ PRODUIT : CATÉGORIES
    // ==============================
    public const CATEGORY_ID = 'c.id';
    public const CATEGORY_SLUG = 'c.slug';
    public const CATEGORY_NAME = 'c.name';
    public const CATEGORY_DESCRIPTION = 'c.description';
    public const CATEGORY_PARENT_ID = 'c.parent_id';

    // ==============================
    // 🖼️ PRODUIT : IMAGE
    // ==============================
    public const PRODUCT_IMAGE_ID = 'pi.id';
    public const PRODUCT_IMAGE_PRODUCT_ID = 'pi.product_id';
    public const PRODUCT_IMAGE_FILE_PATH = 'pi.file_path';
    public const PRODUCT_IMAGE_ALT_TEXT = 'pi.alt_text';
    public const PRODUCT_IMAGE_IS_MAIN = 'pi.is_main';

    // ==============================
    // 🖼️ ROLE
    // ==============================
    public const ROLE_ID = 'r.id';
    public const ROLE_NAME = 'r.name';

    // ==============================
    // 🖼️ USER
    // ==============================
    public const USER_ALL = 'u.*';
    public const USER_ID = 'u.id';
    public const USER_EMAIL = 'u.email';

    // ==============================
    // 🚚 FOURNISSEURS
    // ==============================
    public const SUPPLIER_ID = 's.id';
    public const SUPPLIER_NAME = 's.name';
}

<?php

namespace Src\Modules\Admin\Dashboard\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Config\AppConfig;
use Core\BaseController;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Src\Modules\Category\Domain\Service\CategoryService;
use Src\Modules\User\Domain\Service\UserService;
use Core\Routing\Attribute\Route;
use Exception;

#[Route('/admin/dashboard')]
class DashboardIndexController extends BaseController
{
    public function __construct(
        private CategoryService $categoryService,
        private UserService $userService
    ) {
        parent::__construct('Admin/Dashboard');
    }


    #[Route('', methods: ['GET'])]
    public function index(): void
    {
        $categories = $this->categoryService->getCategoriesTree();
        $this->render('Dashboard/index.twig', [$categories]);
    }


    #[Route('users', methods: ['GET'])]
    public function users(): void
    {
        $users = $this->userService->getAllUsersForAdmin();
        $this->render('Admin/users', [$users]);
    }


    #[Route('products', methods: ['GET'])]
    public function products(): void
    {
        $this->render(__FUNCTION__);
    }

    /**
     * Upload et traitement d'une image (ajout produit, logo, etc.)
     * - Vérifie le type de fichier (sécurité)
     * - Crée automatiquement les dossiers nécessaires
     * - Génère plusieurs tailles optimisées (large, medium, thumbnail)
     * - Supporte plusieurs formats (jpg, png, webp)
     */
    public function uploadImage()
    {
        /*
        $width = filter_var($_POST['width'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 5000]
        ]);

        if ($width === false) {
            throw new InvalidArgumentException("Largeur invalide.");
        }
            */
        // // Étape 1 : vérifier que le fichier est bien uploadé
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Aucun fichier valide reçu.']);
            return;
        }

        // Étape 2 : vérifier que le fichier est réellement une image
        $file = $_FILES['image']['tmp_name'];
        $imageInfo = getimagesize($file);
        // (PHP) getimagesize permet d'analyser le contenu binaire du fichier
        // et s'assure que $imageInfo['mime'] est à 100% une image (non déguisée)
        if ($imageInfo === false) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Le fichier n\'est pas une image valide.']);
            return;
        }

        // Étape 3 : vérifier le type MIME détecté par getimagesize
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($imageInfo['mime'], $allowedMimeTypes, true)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Format d\'image non autorisé.']);
            return;
        }

        // Étape 4 : vérifier que le fichier ne dépasse pas 5 Mo
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            throw new \Exception("Image trop lourde.");
        }

        // Étape 5 : traitement du fichier
        $width  = (int) $imageInfo[0];
        $height = isset($_POST['height']) ? (int) $_POST['height'] : null;
        $format = strtolower($_POST['format'] ?? 'jpg');

        // Lecture de l’image
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);

        // Répertoire de base (doit avoir droits d’écriture → voir AppKernel / config)
        $outputDir = AppConfig::getConst('ROOT_PATH_PUBLIC_UPLOADS') . "img/products/";

        // Rendre le nom du fichier unique
        $uniqueName = $this->generateUniqueFileName($_FILES['image']['name']);

        // Fonction de sauvegarde générique selon format
        $saveImage = function ($img, $path, $format) {
            switch ($format) {
                case 'jpg':
                case 'jpeg':
                    $img->toJpeg(85)->save($path . ".jpg");
                    return ".jpg";
                case 'png':
                    $img->toPng()->save($path . ".png");
                    return ".png";
                case 'webp':
                    $img->toWebp(80)->save($path . ".webp");
                    return ".webp";
                default:
                    throw new \Exception("Format non supporté : $format");
            }
        };

        // Sauvegarde des tailles
        if ($height) {
            $image->resize($width, $height);
        } else {
            $image->scale(width: $width);
        }

        $ext = $saveImage(clone $image, $outputDir . "large/" . $uniqueName, $format);

        (clone $image)->scale(width: 250)->save($outputDir . "medium/" . $uniqueName . $ext);
        (clone $image)->scale(width: 50)->save($outputDir . "thumbnail/" . $uniqueName . $ext);

        // Réponse JSON (pour front JS ou admin panel)
        echo json_encode([
            'status' => 'success',
            'files' => [
                'large'     => "img/products/large/" . $uniqueName . $ext,
                'medium'    => "img/products/medium/" . $uniqueName . $ext,
                'thumbnail' => "img/products/thumbnail/" . $uniqueName . $ext,
            ]
        ]);
    }

    function generateUniqueFileName(string $originalName): string
    {
        $fileName = pathinfo($originalName, PATHINFO_FILENAME);
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($fileName));
        return $slug . '-' . date('Ymd-His') . '-' . uniqid();
    }
}

<?php

namespace App\Controllers\Manage;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use App\Core\Controller;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;

/**
 * ControllersController
 * 
 * This controller is responsible for managing the controllers in the application.
 * It allows listing, creating, and deleting controllers.
 */
class ControllersController extends Controller
{

    public function index()
    {
        $views = $this->listFilesRecursively(BASE_PATH_APP_VIEWS);
        $controllers = $this->listFilesRecursively(BASE_PATH_APP_CONTROLLERS);
        $this->set('title', 'Controllers Dashboard' . BASE_ENTREPRISE_NAME);
        $this->set('controllers', $controllers);
        $this->set('views', $views);
        $this->set('message', 'Administration area. Here you can manage users, settings, and more.');
        // Render the admin index view with the controllers and views data
        // This will use the Twig template engine to render the view
        $this->view('admin/index', $this->data);
    }

    public function read()
    {
        // Read all controller files in the controllers directory
        $controllers = array_diff(scandir(BASE_PATH_APP_CONTROLLERS), ['..', '.', 'AdminController.php']);

        // Filter out only PHP files
        $controllers = array_filter($controllers, function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'php';
        });

        // Remove the '.php' extension from the filenames
        $controllers = array_map(function ($file) {
            return str_replace('.php', '', $file);
        }, $controllers);

        return $controllers;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['controller'])) {
            $controller = $_POST['controller'];

            // Check if the controller file exists
            $filePath = BASE_PATH_APP_CONTROLLERS . $controller . '.php';
            if (file_exists($filePath)) {
                // Delete the controller file
                unlink($filePath);
                echo "<p style='color: green;'>Fichier supprimé avec succès.</p>";
            } else {
                echo "<p style='color: red;'>Fichier non trouvé.</p>";
            }
        } else {
            echo "<p style='color: red;'>Aucun contrôleur sélectionné.</p>";
        }

        // Redirect to the newly created page
        echo "<br><br><br><br><div style='padding: 1em; background: #dff0d8; color: #3c763d; border-radius: 5px; width: fit-content;'>
        ✅ Contrôleur supprimé avec succès. Redirection en cours...
      </div>
      <script>
          setTimeout(function() {
              window.location.href = '" . BASE_URL_MANAGE . "controllers';
          }, 3000);
      </script>";

        exit;
    }

    public function create()
    {
        // Check if the pageName is set and valid
        if (!isset($_POST['pageName'])) {
            die("Nom de page manquant");
        } elseif (empty(trim($_POST['pageName']))) {
            die("Nom de page vide");
        } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['pageName'])) {
            die("Nom de page invalide");
        } else {
            // Sanitize and prepare the page name
            $pageName = ucfirst(strtolower(trim($_POST['pageName']))); // ex : Voiture
            $controllerName = $pageName . 'Controller';
            $viewName = strtolower($pageName);

            $presets = [
                [
                    'source' => BASE_PATH_APP_PRESETS . 'controllers/--replaceController.php',
                    'destination' => BASE_PATH_APP_CONTROLLERS . $controllerName . '.php',
                ],
                [
                    'source' => BASE_PATH_APP_PRESETS . 'views/--replaceView.php',
                    'destination' => BASE_PATH_APP_VIEWS . $viewName . '.php',
                ]
            ];

            // Replace placeholders in the presets
            foreach ($presets as $preset) {
                if (!file_exists($preset['source'])) {
                    die("Fichier preset manquant : " . $preset['source']);
                }

                $content = file_get_contents($preset['source']);

                // Remplacements dans le contenu
                $content = str_replace([
                    '--replaceController',
                    '--replaceView',
                    '--replace'
                ], [
                    $controllerName,
                    $viewName,
                    $pageName
                ], $content);

                // Création du fichier destination
                file_put_contents($preset['destination'], $content);
                echo "<h2>Page créée avec succès : $pageName</h2>";
            }

            // Redirect to the newly created page
            echo " <p>" . $pageName . "Controller.php et " . $viewName . ".php ont été créés avec succès. Redirection dans 3 secondes...</p>
            <script>
            setTimeout(function() {
                window.location.href = '$pageName';
                }, 3000);
                </script>";
            // Redirection vers la nouvelle page après 3 secondes
            exit;
        }
    }

    public function listFilesRecursively($dir)
    {
        $result = [];

        if (!is_dir($dir)) {
            return $result;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $result[] = $file->getFilename(); // juste le nom
            }
        }
        return $result;
    }
}

<?php
// Permet de gérer les sessions des utilisateurs
class SessionController
{
  public function connectionUser()
  {
    include("Controllers/utilities/Encryptor.php");
    include("Controllers/utilities/Database.php");

    $encryptor = new Encryptor();
    $database = new Database("najib");

    # Recupère le mot de passe correspondant au nom d'utilisateur
    $passwordFromDB = $database->selectOne("password", "user", "username", $_POST["username"]);

    # Si un mot de passe a bien été récupéré (que l'utilisateur est bien inscrit en DB)
    if (!empty($passwordFromDB)) {
      # Vérification du mot de passe entré avec celui se trouvant en base de donnée
      if ($encryptor->decryptPassword($_POST['password'], $passwordFromDB)) {
        session_start();
        $_SESSION["username"] = $_POST["username"];
        $_SESSION["admin"] = $database->selectOne("admin", "user", "username", $_POST["username"]);
      } else {
        echo 'Le mot de passe est invalide.';
      }
    } else {
      echo "Vous n'êtes pas dans la base de donnée";
    }
  }

  public function disconnectionUser()
  {
    session_start();
    if (isset($_SESSION["username"]))
      session_unset($_SESSION["username"]);
    session_destroy();
    session_write_close();
  }

  public function getSessionUsername()
  {
    session_start();
    return $_SESSION["username"];
  }

  public function getSessionAdmin()
  {
    session_start();
    return $_SESSION["admin"];
  }
}

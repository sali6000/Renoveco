<?php
// Exception/Domain/DomainExceptionInterface.php

namespace Src\Exception\Domain;

/**
 * Contrat commun pour toutes les exceptions métier de l'application.
 *
 * Permet de catcher en un seul bloc toutes les exceptions domaine
 * sans lister chaque type explicitement.
 *
 * @see AuthentificationException // Erreur d'authentification
 * @see UniqueConstraintException // Erreur PDO
 * @see MailException // Erreur PHPMailer
 * ...
 */
interface DomainExceptionInterface extends \Throwable
{
    public function getMessage(): string;
    public function getErrorCode(): string;
}

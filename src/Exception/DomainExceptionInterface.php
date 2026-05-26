<?php

namespace Src\Exception;

use Throwable;

/**
 * Contrat commun pour toutes les exceptions métier de l'application.
 *
 * Permet de catcher en un seul bloc toutes les exceptions domaine
 * sans lister chaque type explicitement.
 *
 * @see ValidationException
 * @see ServiceException
 * @see UniqueConstraintException
 */
interface DomainExceptionInterface extends \Throwable
{
    /**
     * Retourne le code métier de l'exception.
     *
     * Permet de distinguer les types d'échec sans parser le message.
     * Exemple : 'MAIL_FAILED', 'RATE_LIMIT', 'USER_EMAIL'
     */
    public function getErrorCode(): string;
}

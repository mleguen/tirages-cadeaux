<?php

declare(strict_types=1);

namespace App\Application\Actions\Profil;

use App\Application\Actions\Action;
use App\Domain\Utilisateur\UtilisateurRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class GetProfilAction extends Action
{
  /**
   * @var UtilisateurRepository
   */
  protected $utilisateurRepository;

  /**
   * @param LoggerInterface $logger
   * @param UtilisateurRepository  $utilisateurRepository
   */
  public function __construct(LoggerInterface $logger, UtilisateurRepository $utilisateurRepository)
  {
    parent::__construct($logger);
    $this->utilisateurRepository = $utilisateurRepository;
  }

  protected function action(): Response
  {
    // TODO: lire l'id à renvoyer depuis le JWT
    $utilisateur = $this->utilisateurRepository->findUtilisateurOfId(0);
    return $this->respondWithData($utilisateur->getUtilisateurSansMdp());
  }
}

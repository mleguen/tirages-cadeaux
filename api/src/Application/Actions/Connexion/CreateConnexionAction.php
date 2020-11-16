<?php

declare(strict_types=1);

namespace App\Application\Actions\Connexion;

use App\Application\Actions\Action;
use App\Application\Service\AuthService;
use App\Domain\Utilisateur\UtilisateurNotFoundException;
use App\Domain\Utilisateur\UtilisateurRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class CreateConnexionAction extends Action
{
  /**
   * @var UtilisateurRepository
   */
  protected $utilisateurRepository;

  /**
   * @var AuthService
   */
  protected $authService;

  /**
   * @param LoggerInterface $logger
   * @param UtilisateurRepository  $utilisateurRepository
   */
  public function __construct(LoggerInterface $logger, UtilisateurRepository $utilisateurRepository, AuthService $authService)
  {
    parent::__construct($logger);
    $this->utilisateurRepository = $utilisateurRepository;
    $this->authService = $authService;
  }

  protected function action(): Response
  {
    $body = $this->getFormData();
    try {
      $utilisateur = $this->utilisateurRepository->readOneByIdentifiants($body['identifiant'], $body['mdp']);
      $id = $utilisateur->getId();
      $nom = $utilisateur->getNom();
      $estAdmin = $utilisateur->getEstAdmin();
      $this->logger->info("Utilisateur $id ($nom) connecté" . ($estAdmin ? " (admin)" : ""));

      return $this->respondWithData([
        "token" => $this->authService->encodeAuthToken($utilisateur->getId(), $estAdmin),
        "utilisateur" => [
          "id" => $id,
          "nom" => $nom,
          "estAdmin" => $estAdmin,
        ]
      ]);
    }
    catch (UtilisateurNotFoundException $err) {
      throw new HttpBadRequestException($this->request, "identifiants invalides");
    }
    catch (\Exception $e) {
      $this->logger->error($e->getMessage());
      throw $e;
    }
  }
}

<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Repository\VersionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/api/stats', name: 'app_stats')]
    public function index(GameRepository $gameRepository, VersionRepository $versionRepository, UserRepository $userRepository): Response
    {
        $gameCount = $gameRepository->count();
        $versionCount = $versionRepository->count();
        $userCount = $userRepository->count();

        return $this->json(["games" => $gameCount, "versions" => $versionCount, "users" => $userCount]);
    }
}

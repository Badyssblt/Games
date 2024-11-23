<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    #[Route('/api/logout', name: 'app_auth')]
    public function logout(): Response
    {

        $response = new Response();

        $cookie = new Cookie(
            'token',           // Nom du cookie
            null,              // Valeur du cookie (null ou vide pour le supprimer)
            time() - 3600,     // Expiration dans le passé
            '/',               // Path
            null,              // Domaine (null pour utiliser le domaine actuel)
            true,              // Secure
            true,              // HttpOnly
            false,             // Pas de partitionnement
            false              // Pas de SameSite
        );

        $response->headers->setCookie($cookie);

        return $response;
    }
}

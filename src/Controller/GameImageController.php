<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Vich\UploaderBundle\Handler\UploadHandler;

class GameImageController extends AbstractController
{
    private $entityManager;
    private $uploadHandler;

    public function __construct(EntityManagerInterface $entityManager, UploadHandler $uploadHandler)
    {
        $this->entityManager = $entityManager;
        $this->uploadHandler = $uploadHandler;
    }

    public function __invoke(Request $request): Response
    {
        $game = new Game();

        $game->setName($request->get('name'));
        $game->setDescription($request->get('description'));
        $game->setVersion($request->get('version'));
        $game->setSize($request->get('size'));

        $file = $request->files->get('imageFile');
        if ($file) {
            $game->setImageFile($file);

            $this->uploadHandler->upload($game, 'imageFile');
        }

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $this->json($game, Response::HTTP_CREATED, [], ['groups' => ['game:read']]);
    }
}

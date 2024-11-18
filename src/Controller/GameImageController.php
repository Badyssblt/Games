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

    public function __invoke(Game $game, Request $request): Response
    {
        $file = $request->files->get('imageFile');
        if ($file) {
            $game->setImageFile($file);

            $this->uploadHandler->upload($game, 'imageFile');

            $game->setUpdatedAt(new \DateTimeImmutable('now'));
        }

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $this->json($game, Response::HTTP_CREATED, [], ['groups' => ['game:read']]);
    }
}

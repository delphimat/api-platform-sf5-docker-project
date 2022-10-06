<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordEncoderInterface $userPasswordEncoder
    ) {
    }

    public function __invoke(Request $request, User $data): Response
    {
        $users = $this->userRepository->findBy(['username' => $data->getUsername()]);
        if (count($users)) {
            return $this->json([], Response::HTTP_BAD_REQUEST);
        }

        $data->setPassword($this->userPasswordEncoder->encodePassword($data, $data->getPassword()));
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $this->json(['id' => $data->getId()], Response::HTTP_CREATED);
    }
}

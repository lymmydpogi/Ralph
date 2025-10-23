<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormError;



#[Route('/users')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(UsersRepository $usersRepository): Response
    {
        $users = $usersRepository->findAll();
        return $this->render('users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If this is a customer user, assign a role
            $user->setRoles(['ROLE_CLIENT']);
            try {
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'User (Customer) created successfully!');
                return $this->redirectToRoute('user_index');
            } catch (UniqueConstraintViolationException $e) {
                $form->get('email')->addError(new FormError('This email is already registered.'));
                $this->addFlash('danger', 'Cannot create user: email already exists.');
            }
        }

        return $this->render('users/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(Users $user): Response
    {
        return $this->render('users/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Users $user, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->flush();
                $this->addFlash('success', 'User updated successfully!');
                return $this->redirectToRoute('user_index');
            } catch (UniqueConstraintViolationException $e) {
                $form->get('email')->addError(new FormError('This email is already registered.'));
                $this->addFlash('danger', 'Cannot update user: email already exists.');
            }
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, Users $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'User deleted successfully!');
        }

        return $this->redirectToRoute('user_index');
    }
}

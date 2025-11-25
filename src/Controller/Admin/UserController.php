<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/', name: 'admin_users')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['created_at' => 'DESC']);
        
        // Séparer les utilisateurs par rôle
        $admins = array_filter($users, fn($user) => in_array('ROLE_ADMIN', $user->getRoles()));
        $employees = array_filter($users, fn($user) => in_array('ROLE_EMPLOYEE', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles()));
        $regularUsers = array_filter($users, fn($user) => !in_array('ROLE_ADMIN', $user->getRoles()) && !in_array('ROLE_EMPLOYEE', $user->getRoles()));
        
        return $this->render('admin/users/index.html.twig', [
            'admins' => $admins,
            'employees' => $employees,
            'regular_users' => $regularUsers,
            'all_users' => $users,
        ]);
    }
    
    #[Route('/new', name: 'admin_user_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user, ['is_new' => true]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
            
            // Définir les rôles en fonction du type sélectionné
            $userType = $form->get('userType')->getData();
            switch ($userType) {
                case 'admin':
                    $user->setRoles(['ROLE_ADMIN', 'ROLE_EMPLOYEE']);
                    break;
                case 'employee':
                    $user->setRoles(['ROLE_EMPLOYEE']);
                    break;
                default:
                    $user->setRoles([]);
                    break;
            }
            
            $em->persist($user);
            $em->flush();
            
            $this->addFlash('success', 'Utilisateur créé avec succès !');
            return $this->redirectToRoute('admin_users');
        }
        
        return $this->render('admin/users/new.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'admin_user_edit')]
    public function edit(
        int $id,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $userRepository->find($id);
        
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        
        // Empêcher l'utilisateur de modifier son propre compte depuis cette interface
        if ($user === $this->getUser()) {
            $this->addFlash('warning', 'Vous ne pouvez pas modifier votre propre compte depuis cette interface.');
            return $this->redirectToRoute('admin_users');
        }
        
        $form = $this->createForm(UserFormType::class, $user, ['is_new' => false]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Si un nouveau mot de passe est fourni
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
            
            // Mettre à jour les rôles
            $userType = $form->get('userType')->getData();
            switch ($userType) {
                case 'admin':
                    $user->setRoles(['ROLE_ADMIN', 'ROLE_EMPLOYEE']);
                    break;
                case 'employee':
                    $user->setRoles(['ROLE_EMPLOYEE']);
                    break;
                default:
                    $user->setRoles([]);
                    break;
            }
            
            $em->flush();
            
            $this->addFlash('success', 'Utilisateur modifié avec succès !');
            return $this->redirectToRoute('admin_users');
        }
        
        return $this->render('admin/users/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
    
    #[Route('/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(
        int $id,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ): Response {
        $user = $userRepository->find($id);
        
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        
        // Empêcher l'utilisateur de supprimer son propre compte
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('admin_users');
        }
        
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }
        
        return $this->redirectToRoute('admin_users');
    }
    
    #[Route('/{id}/toggle-status', name: 'admin_user_toggle_status', methods: ['POST'])]
    public function toggleStatus(
        int $id,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ): Response {
        $user = $userRepository->find($id);
        
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        
        // Empêcher l'utilisateur de désactiver son propre compte
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas désactiver votre propre compte.');
            return $this->redirectToRoute('admin_users');
        }
        
        // Toggle le statut (vous pouvez ajouter un champ is_active dans l'entité User si nécessaire)
        // Pour l'instant, on va juste afficher un message
        $this->addFlash('info', 'Fonctionnalité de désactivation à implémenter.');
        
        return $this->redirectToRoute('admin_users');
    }
}

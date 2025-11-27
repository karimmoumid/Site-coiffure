<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        MailerInterface $mailer,
        ValidatorInterface $validator
    ): Response {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $subject = $request->request->get('subject');
            $message = $request->request->get('message');
            
            // Validation simple
            $errors = [];
            
            if (empty($name)) {
                $errors[] = 'Le nom est requis';
            }
            
            $emailConstraint = new Assert\Email();
            $emailErrors = $validator->validate($email, $emailConstraint);
            if (count($emailErrors) > 0 || empty($email)) {
                $errors[] = 'Email invalide';
            }
            
            if (empty($subject)) {
                $errors[] = 'Le sujet est requis';
            }
            
            if (empty($message)) {
                $errors[] = 'Le message est requis';
            }
            
            if (empty($errors)) {
                try {
                    $emailMessage = (new Email())
                        ->from('noreply@salonsana.com')
                        ->to('moumidmounir@gmail.com')
                        ->subject('Contact : ' . $subject)
                        ->html("
                            <h3>Nouveau message de contact</h3>
                            <p><strong>Nom :</strong> {$name}</p>
                            <p><strong>Email :</strong> {$email}</p>
                            <p><strong>Sujet :</strong> {$subject}</p>
                            <p><strong>Message :</strong></p>
                            <p>{$message}</p>
                        ");
                    
                    $mailer->send($emailMessage);
                    
                    $this->addFlash('success', 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.');
                    return $this->redirectToRoute('app_contact');
                } catch (\Exception $e) {
                    $errors[] = 'Erreur lors de l\'envoi du message';
                }
            }
            
            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }
        
        return $this->render('contact/index.html.twig');
    }
}

<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = $options['is_new'];
        
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom complet',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Jean Dupont'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est requis']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'email@exemple.com'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est requis']),
                    new Email(['message' => 'Veuillez entrer un email valide']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => $isNew ? 'Mot de passe' : 'Nouveau mot de passe (laisser vide pour ne pas changer)',
                'required' => $isNew,
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password',
                    'placeholder' => $isNew ? 'Mot de passe sécurisé' : 'Laisser vide pour conserver l\'actuel'
                ],
                'constraints' => $isNew ? [
                    new NotBlank(['message' => 'Le mot de passe est requis']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ] : [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('userType', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Type d\'utilisateur',
                'choices' => [
                    'Utilisateur standard' => 'user',
                    'Employé' => 'employee',
                    'Administrateur' => 'admin',
                ],
                'data' => $this->getUserType($builder->getData()),
                'attr' => ['class' => 'form-select'],
                'help' => 'Les administrateurs ont tous les droits, les employés peuvent gérer les rendez-vous et le stock',
            ])
        ;
    }
    
    private function getUserType($user): string
    {
        if (!$user instanceof User) {
            return 'user';
        }
        
        $roles = $user->getRoles();
        
        if (in_array('ROLE_ADMIN', $roles)) {
            return 'admin';
        } elseif (in_array('ROLE_EMPLOYEE', $roles)) {
            return 'employee';
        }
        
        return 'user';
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_new' => false,
        ]);
    }
}

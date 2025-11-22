<?php

namespace App\Form;

use App\Entity\Appointement;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class AppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom complet',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre nom et prénom'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre nom',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'votre.email@exemple.com'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre email',
                    ]),
                    new Email([
                        'message' => 'Veuillez entrer un email valide',
                    ]),
                ],
            ])
            ->add('services', EntityType::class, [
    'class' => Service::class,
    'choice_label' => fn(Service $s) => $s->getName() . ' - ' . $s->getPrice() . '€ (' . $s->getTime() . ' min)',
    'multiple' => true,      // <- obligatoire
    'expanded' => false,     // true = checkboxes
    'placeholder' => 'Sélectionnez un service',
])

             ->add('date', DateType::class, [
        'mapped' => false,
        'label' => 'Date souhaitée',
        'widget' => 'single_text',
        'attr' => ['class' => 'form-control', 'min' => (new \DateTime())->format('Y-m-d')],
        'constraints' => [
            new NotBlank(['message' => 'Veuillez sélectionner une date']),
            new GreaterThanOrEqual(['value' => 'today', 'message' => 'La date doit être aujourd\'hui ou dans le futur']),
        ],
    ])
    ->add('time', TimeType::class, [
        'mapped' => false,
        'label' => 'Heure souhaitée',
        'widget' => 'single_text',
        'attr' => ['class' => 'form-control'],
        'constraints' => [
            new NotBlank(['message' => 'Veuillez sélectionner une heure']),
        ],
    ])
            ->add('comment', TextareaType::class, [
                'label' => 'Notes ou demandes particulières',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Indiquez toute information utile pour votre rendez-vous...'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointement::class,
        ]);
    }
}

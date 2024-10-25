<?php

namespace App\Form;

use App\Entity\ApplicationStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationResponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $applicantName = $options['applicant_name'] ?? 'Applicant';

        $builder
            ->add('response', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Dear $applicantName...",
                    'rows' => 10,
                ]
            ])
            ->add('application_status', EntityType::class, [
                'class' => ApplicationStatus::class,
                'label' => 'Set status of application (will not be send in email)',
                'data' => $options['application_status'] // Set select to current application status
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);

        $resolver->setDefined(['applicant_name', 'application_status']);
    }
}

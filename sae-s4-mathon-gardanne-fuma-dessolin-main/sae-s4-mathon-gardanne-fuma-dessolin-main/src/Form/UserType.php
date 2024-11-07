<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login')
            ->add('password', PasswordType::class,['constraints' => $options['required'] ? [
                "new NotBlank()"
            ] : [],
            'required' => $options['required'],
            'empty_data' => ""
        ])
            ->add('admin', CheckboxType::class, ['mapped'=> false,'required' => false,])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
        $resolver->setDefaults([
            'required' => true // set required to true by default
        ]);

        $resolver->setRequired('required'); // add a required option
    }
}

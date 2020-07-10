<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['mapped' => false])
            ->add('name', TextType::class)
            ->add('address', TextType::class)
            ->add('city', TextType::class)
            ->add('zipCode', TextType::class)
            ->add('phoneNumber', TelType::class)
            ->add('siret', TextType::class)
            ->add('lastName', TextType::class)
            ->add('firstName', TextType::class)
            ->add('save', SubmitType::class, ['attr' => ['class' => 'btn btn-neymo btn-create']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}

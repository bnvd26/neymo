<?php

namespace App\Form;

use App\Entity\Particular;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticularType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('address', TextType::class)
            ->add('city', TextType::class)
            ->add('zipCode', TextType::class)
            ->add('phoneNumber', TelType::class)
            ->add('birthdate', DateType::class, [
                    'widget' => 'choice',
                    'years' => range(date('Y')-100, date('Y')-17),
                    'months' => range(date('m'), 12),
                    'days' => range(date('d'), 31),
            ])
            ->add('save', SubmitType::class, ['attr' => ['class' => 'btn btn-neymo btn-create']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Particular::class,
        ]);
    }
}

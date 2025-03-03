<?php

namespace App\Form;

use App\Entity\Adress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class AdressOrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder,array $options): void
    {
        $builder
            ->add('country',TextType::class,[
                'attr' => ['class' => 'reversedColor',
                            'placeholder' => 'Pays de residence',],
            ])
            ->add('zipcode',NumberType::class,[
                'required' => true,
                'attr' => [ 'class' => 'reversedColor symfonyInput',
                            'placeholder' => 'Votre code postal',
                            'pattern' =>"\d{4,4}"
                ],
            ])
            ->add('streetAdress', TextType::class, [
                'attr' => ['class' => 'reversedColor ',
                            'placeholder' => 'Votre adresse postale'],
            ])
            ->add('city', TextType::class, [
                'attr' => ['class' => 'reversedColor symfonyInput',
                            'placeholder' => 'Votre Ville'],
            ])
            ->add('numBatiment',TextType::class ,[
                'attr' => ['class' => 'reversedColor symfonyInput',
                            'placeholder' => 'Votre num de batiment'],
                'required'=>false, 
                    'constraints' => [
                        new Length([
                            'min' => 0,
                            'maxMessage' => 'maximum {{ limit }} characters',
                            'max' => 6,
                        ]),
                    ],
            ])
            ->add('appartementNumber',TextType::class,[
                'attr' => ['class' => 'reversedColor symfonyInput',
                            'placeholder' => 'Votre num appartement'],
                'required'=>false, 
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adress::class,
        ]);
    }
}

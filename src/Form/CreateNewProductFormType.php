<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class CreateNewProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder,array $options): void
    {
        $builder
            ->add('productName',TextType::class,[
                'attr' => ['class' => 'reversedColor',
                            'placeholder' => 'Nom du produit'],
            ])
            ->add('description',TextType::class,[
                'attr' => ['class' => 'reversedColor symfonyInput',
                            'placeholder' => 'description du produit'],
            ])
            ->add('unitPrice', TextType::class, [
                'attr' => ['class' => 'reversedColor ',
                            'placeholder' => 'prix du produit'],
            ])
            ->add('color', TextType::class, [
                'attr' => ['class' => 'reversedColor symfonyInput',
                            'placeholder' => 'Couleur en hexa'],
            ])
            ->add('collectionId', TextType::class, [
                'attr' => ['class' => 'reversedColor symfonyInput',
                            'placeholder' => 'Id de la collection'],
            ])
            ->add('modelName',TextType::class,[
                'attr' => ['class' => 'reversedColor symfonyInput',
                            'placeholder' => 'nom du modele'],
            ])
            ->add('labels',TextType::class,[
                'attr' => ['class' => 'reversedColor symfonyInput',
                            'placeholder' => 'les labels'], 
            ])
            ->add('productImg', FileType::class, [
                'label' => 'Image du produit ( image )',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'veuillez renseigner une image au format valide (jpeg ou png)',
                    ])
                ],
            ])
            ->add('unitPrice', TextType::class, [
                'attr' => ['class' => 'reversedColor ',
                            'placeholder' => 'prix du produit'],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantity',
                'required' => true,  // Ce champ n'est pas obligatoire
                'mapped' => false,  // Le champ n'est pas lié à une propriété de l'entité Product
                'attr' => [
                    'min' => 1,  // Valeur minimale de 1
                    'max' => 1000, // Valeur maximale de 1000
                    'class' => 'reversedColor symfonyInput',
                    'placeholder' => 'ex : 50',
                ],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Livro;
use App\Entity\Autor;
use App\Entity\Assunto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivroType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título',
                'attr' => ['maxlength' => 40],
            ])
            ->add('editora', TextType::class, [
                'label' => 'Editora',
                'attr' => ['maxlength' => 40],
            ])
            ->add('edicao', IntegerType::class, [
                'label' => 'Edição',
                'attr' => ['min' => 1],
            ])
            ->add('anoPublicacao', TextType::class, [
                'label' => 'Ano de publicação',
                'attr' => [
                    'maxlength' => 4,
                    'placeholder' => 'Ex.: 2026',
                    'inputmode' => 'numeric',
                ],
            ])
            ->add('valor', MoneyType::class, [
                'label' => 'Valor (R$)',
                'currency' => 'BRL',
                'divisor' => 1,
                'scale' => 2,
                'html5' => false,
                'grouping' => false,
                'attr' => [
                    'class' => 'valor-monetario',
                    'placeholder' => '0,00',
                    'inputmode' => 'numeric',
                    'maxlength' => 11,
                    'autocomplete' => 'off',
                ],
            ])
            ->add('autores', EntityType::class, [
                'class' => Autor::class,
                'choice_label' => 'nome',
                'label' => 'Autores',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
            ])
            ->add('assuntos', EntityType::class, [
                'class' => Assunto::class,
                'choice_label' => 'descricao',
                'label' => 'Assuntos',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults([
            'data_class' => Livro::class,
        ]);
    }
}
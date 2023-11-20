<?php

namespace App\Form;

use App\Entity\Word;
use App\Validator\CheckRusLetter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\LessThan;

class RelatedWordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Введите слово или словосочетения'
                    ]),
                    new CheckRusLetter()
                ],
            ])
            ->add('count', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Введите число не более 4'
                    ]),
                    new LessThan([
                        'value' => 5,
                        'message' => 'Введите число не более 4',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Word::class,
        ]);
    }
}

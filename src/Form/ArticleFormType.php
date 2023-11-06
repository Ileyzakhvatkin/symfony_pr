<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Module;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('theme', ChoiceType::class, [
                'choices'  => [
                    'Про НЕ здоровую еду' => 'FOOD',
                    'Про PHP и как с этим жить' => 'PHP',
                    'Про женщин и не только' => 'WOMEN',
                ],
                'placeholder' => 'Выберете тему',
            ])
            ->add('module', EntityType::class, [
                'class' => Module::class,
                'choice_label' => 'title',
                'placeholder' => 'Выберете модуль',
                'choices' => $moduleRepository->listAuthUser($authUser->getId())
            ])
            ->add('keyword', TextType::class)
            ->add('keyword_dist', TextType::class)
            ->add('keyword_many', TextType::class)
            ->add('size', NumberType::class, ['attr' => ['maxlength' => 4]])
            ->add('maxsize', NumberType::class, ['attr' => ['maxlength' => 4]])
//        ;
//
//        if (isset($article) && count($article->getWords()) > 0) {
//            foreach ($article->getWords() as $key => $el) {
//                $formArt
//                    ->add('word_' . $key + 1, TextType::class)
//                    ->add('word_count_' . $key + 1, NumberType::class);
//            }
//        }
//        $formArt
            ->add('word_0', TextType::class)
            ->add('word_count_0', NumberType::class, ['attr' => ['maxlength' => 2]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}

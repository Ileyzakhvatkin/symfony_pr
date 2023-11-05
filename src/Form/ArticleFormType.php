<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('theme')
            ->add('title')
            ->add('key_word')
            ->add('key_word_dist')
            ->add('key_word_many')
            ->add('min_size')
            ->add('max_size')
            ->add('content')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('user')
            ->add('module')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}

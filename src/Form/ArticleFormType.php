<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Module;
use App\Entity\Word;
use App\Repository\ModuleRepository;
use App\Services\WordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ArrayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ArticleFormType extends AbstractType
{
    private ModuleRepository $moduleRepository;

    public function __construct(ModuleRepository $moduleRepository)
    {
        $this->moduleRepository = $moduleRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Article $article */
        $article = $options['data'] ?? null;

        $builder
            ->add('title', TextType::class, [
                'required' => false,
            ])
            ->add('theme', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Про НЕ здоровую еду' => 'FOOD',
                    'Про PHP и как с этим жить' => 'PHP',
                    'Про женщин и не только' => 'WOMEN',
                ],
                'placeholder' => 'Выберете тему',
            ])
            ->add('module', EntityType::class, [
                'required' => false,
                'class' => Module::class,
                'choice_label' => 'title',
                'placeholder' => 'Выберете модуль',
                'choices' => $this->moduleRepository->listAuthUser()
            ])
            ->add('size', NumberType::class, [
                'required' => false,
                'attr' => ['maxlength' => 4],
            ])
            ->add('maxsize', NumberType::class, [
                'required' => false,
                'attr' => ['maxlength' => 4],
            ])
            ->add('images', FileType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => true,
//                'constraints' => new Image([
//                        'maxSize' => '1M',
//                        'maxSizeMessage' => 'Размер файла не должен быть больше 1M'
//                    ])
            ]);

        $keywords = ['keyword0', 'keyword1', 'keyword2', 'keyword3', 'keyword4', 'keyword5', 'keyword6'];
        foreach ($keywords as $key => $word) {
            $builder->add($word, TextType::class, [
                'mapped' => false,
                'required' => false,
                'data' => isset($article) ? $article->getKeyword()[$key] : '',
            ]);
        }

//        $builder->add('words', CollectionType::class, [
//            'entry_type' => WordType::class,
//            'allow_add' => true,
//            'allow_delete' => true,
//            'by_reference' => false,
//            'prototype' => true,
//            'prototype_name' => '__relatedentities__',
//            'attr' => ['class' => 'related-entities'],
//        ]);

//        if (isset($article) && count($article->getWords()) > 0) {
//            $entryOptions = [];
//            foreach ($article->getWords() as $key=>&$el) {
//                $entryOptions[] = $builder->create('wordsFields_' . $key, FormType::class)
//                    ->add('word', TextType::class, [
//                        'required' => false,
//                        'mapped' => false,
//                        'data' => $el->getTitle()
//                    ])
//                    ->add('count', NumberType::class, [
//                        'required' => false,
//                        'mapped' => false,
//                        'attr' => ['maxlength' => 2],
//                        'data' => $el->getCount()
//                    ]);
//            }
//
//            $builder->add('words', CollectionType::class, [
//                    'required' => false,
//                    'mapped' => false,
//                    'data' => $entryOptions,
//                    'allow_add' => true,
//                ]
//            );
//        }

//        if (isset($article) && count($article->getWords()) > 0) {
//            foreach ($article->getWords() as $key=>$el) {
//                $builder->add(
//                    $builder->create('words', FormType::class)->add(
//                        $builder->create('wordsFields_' . $key, FormType::class)
//                            ->add('word', TextType::class, [
//                                'required' => false,
//                                'mapped' => false,
//                                'data' => $el->getTitle()
//                            ])
//                            ->add('count', NumberType::class, [
//                                'required' => false,
//                                'attr' => ['maxlength' => 2],
//                                'mapped' => false,
//                                'data' => $el->getCount()
//                            ])
//                    )
//                );
//            }
//        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}

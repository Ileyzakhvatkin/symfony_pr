<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Module;
use App\Repository\ModuleRepository;
use App\Validator\CheckRusNoun;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                'constraints' => [
                    new Count([
                        'max' => 5,
                        'maxMessage' => 'Не более пяти изображений',
                    ]),
                    new All([
                        new Image([
                            'maxSize' => '1M',
                            'maxSizeMessage' => 'Слишком большое изображение',
                        ]),
                    ])
                ]
            ]);

        $keywords = ['keyword0', 'keyword1', 'keyword2', 'keyword3', 'keyword4', 'keyword5', 'keyword6'];
        foreach ($keywords as $key => $word) {
            if ($key === 0) {
                $builder->add($word, TextType::class, [
                    'mapped' => false,
                    'required' => false,
                    'data' => isset($article) ? $article->getKeyword()[$key] : '',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Введите ключевое слово для статьи'
                        ]),
                        new CheckRusNoun(),
                    ]
                ]);
            } else {
                $builder->add($word, TextType::class, [
                    'mapped' => false,
                    'required' => false,
                    'data' => isset($article) ? $article->getKeyword()[$key] : '',
                ]);
            }
        }

        $builder->add(
            $builder->create('words', CollectionType::class, [
                'entry_type' => RelatedWordType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => 'RelatedWords',
                'attr' => [ 'class' => 'related-words' ],
            ])
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}

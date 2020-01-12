<?php
namespace App\Form;

use App\Entity\News;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class NewsFormType extends AbstractType
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class, [
            'invalid_message' => 'Just no!'
        ])
        ->add('short', TextType::class, [
            'invalid_message' => 'Just no!'
        ])
        ->add('content', TextareaType::class, [
            'invalid_message' => 'Just no!',
        ])
        ->add('categories', EntityType::class, [
          'class' => Category::class,
          'multiple' => true,
          'choice_label' => function(Category $user) {
              return sprintf('(%d) %s', $user->getId(), $user->getTitle());
          },
        ])
        ->add('picture', FileType::class, [
                'mapped' => false,
                'required' => false
          ])
        ;
    }
}

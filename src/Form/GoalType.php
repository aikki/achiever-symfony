<?php

namespace App\Form;

use App\Entity\Goal;
use App\Service\IconsProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GoalType extends AbstractType
{
    private $iconsProvider;
    public function __construct(IconsProvider $iconsProvider)
    {
        $this->iconsProvider = $iconsProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('iconClassName', ChoiceType::class, [
                'choices' => $this->iconsProvider->generate(),
                'choice_label' => function ($choice, $key, $value) {
                    return "<i class='$value'></i>";
                },
                'expanded' => true,
                'multiple' => false,
                'label_html' => true,
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'btn-check'];
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Goal::class,
        ]);
    }
}

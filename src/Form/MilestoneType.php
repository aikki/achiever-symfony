<?php

namespace App\Form;

use App\Entity\Goal;
use App\Entity\Milestone;
use App\Service\IconsProvider;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MilestoneType extends AbstractType
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
            ->add('goals', EntityType::class, [
                'class' => Goal::class,
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function($r) use ($options) {
                    return $r->createQueryBuilder('g')->andWhere('g.club = :club')->setParameter('club', $options['club']);
                }
            ])
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
            'data_class' => Milestone::class,
            'club' => null,
        ]);
    }
}

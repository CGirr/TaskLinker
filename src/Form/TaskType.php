<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\Task;
use App\Enum\TaskStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;

/**
 *
 */
#[AsEntityAutocompleteField]
class TaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'placeholder' => 'Ce champ est obligatoire',
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Aucune description',
                ],
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'input' => 'datetime_immutable',
                'html5' => true,
                'attr' => [
                    'placeholder' => 'Choisir une date',
                ],
            ])
            ->add('status', EnumType::class, [
                'class' => TaskStatus::class,
                'choice_label' => 'value',
            ])
            ->add('member', EntityType::class, [
                'class' => Employee::class,
                'choices' => $options['members'],
                'choice_label' => function (Employee $employee) {
                    return $employee->getFirstname() . ' ' . $employee->getLastname();
                },
                'required' => false,
                'attr' => [
                    'data-controller' => 'symfony--ux-autocomplete--autocomplete',
                    'data-symfony--ux-autocomplete--autocomplete-max-results-value' => 10,
                    'data-symfony--ux-autocomplete--autocomplete-preload-value' => 'focus',
                    'data-symfony--ux-autocomplete--autocomplete-multiple-value' => 'false',
                    'placeholder' => 'Choisir un membre...',
                    'class' => 'form-control',
                ],
            ])
            ->get('date')
                ->addModelTransformer(new CallbackTransformer(
                    fn($date) => $date,
                    fn($date) => $date ?: null
                ));
        ;
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'members' => [],
        ]);
    }
}

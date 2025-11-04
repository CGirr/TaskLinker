<?php

namespace App\Form;

use App\Entity\Employee;
use App\Enum\EmployeeRole;
use App\Enum\EmployeeStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 */
class EmployeeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class, [
                'choices' => EmployeeRole::cases(),
                'choice_label' => fn(EmployeeRole $role) => $role->getLabel(),
                'choice_value' => fn(?EmployeeRole $role) => $role?->value,
                'expanded' => true,
                'required' => true,
                ])
            ->add('status', EnumType::class, [
                'class' => EmployeeStatus::class,
                'choice_label' => 'value',
            ])
            ->add('entry_date', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'html5' => true,
            ]);

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                fn($rolesArray) => $rolesArray[0] ? EmployeeRole::from($rolesArray[0]) : null,
                fn(?EmployeeRole $role) => $role ? [$role->value] : []
            ));
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}

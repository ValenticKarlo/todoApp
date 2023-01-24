<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Repository\TodoListRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('task', TextType::class, [
                'attr'=> array(
                    'placeholder' => 'Enter Task'
                ),
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a Task',
                    ]),
                ]
            ])
            ->add('priority', ChoiceType::class, [
                'choices'=>[
                    'High priority'=>1,
                    'Medium priority'=>2,
                    'Low priority'=>3,
                ],
            ])
            ->add('deadline',DateTimeType::class,[
                'attr'=>[
                    'class'=>'data-mdb-inline'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'choices'=>[
                    'Task not completed'=>false,
                    'Task completed'=>true
                ],
            ])
            ->add('todoList', EntityType::class, [
               'class'=> TodoList::class,
                'choices'=>$options['listRepo'],
                'choice_label'=> 'name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'listRepo' => array()
        ]);
        $resolver->setAllowedTypes('listRepo', ['array', 'null']);
    }
}

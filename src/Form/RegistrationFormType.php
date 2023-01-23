<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Length([
                        'min' => '1',
                        'minMessage' => 'Your name should be at least 2 characters'
                    ]),
                    new NotBlank([
                        'message' => 'Please enter a First name',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Firstname'
                ],
            ])
            ->add('surname', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Lastname'
                ],
                'constraints' => [
                    new Length([
                        'min' => '1',
                        'minMessage' => 'Your lastname should be at least 2 characters'
                    ]),
                    new NotBlank([
                        'message' => 'Please enter a last name',
                    ]),
                ]
            ])
            ->add('email', TextType::class, [
                'label' => false,
                'attr' => [
                    'autocomplete' => 'email',
                    'placeholder' => 'Email'
                ],
                'constraints' => [
                    new Email([
                        'message' => 'This email is not valid email.'
                    ]),
                    new NotBlank([
                        'message' => 'Please enter a email',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' => 'The passwords fields must match.',
                'required' => true,
                'mapped' => false,
                'options' => [
                    'mapped' => false,
                    'label' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                    ],
                ],
                'first_options' => [
                    'attr' => [
                        'placeholder' => 'Password',
                        'autocomplete' => 'new-password',
                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => 'Repeat password',
                        'autocomplete' => 'new-password',
                    ],
                ],
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
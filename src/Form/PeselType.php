<?php

namespace App\Form;

use App\Validator\Pesel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PeselType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('pesel', TextType::class, [
            'label' => 'Numer PESEL',
            'constraints' => [new NotBlank(), new Pesel()],
            'attr' => ['maxlength' => 11],
        ]);
    }
}

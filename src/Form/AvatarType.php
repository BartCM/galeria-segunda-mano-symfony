<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class AvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('avatar', FileType::class, [
            'mapped' => false,
            'label' => 'Cambiar avatar',
            'attr' => [
                'id' => 'avatar-input',
                'accept' => 'image/png, image/jpeg',
                'onchange' => 'previewAvatar(event)',
            ],
            'constraints' => [
                new File(
                    maxSize: '10k',
                    mimeTypes: ['image/jpeg', 'image/png'],
                    mimeTypesMessage: 'Solo JPG o PNG',
                )
            ],
        ]);
    }
}
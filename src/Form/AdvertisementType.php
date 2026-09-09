<?php

namespace App\Form;

use App\Entity\Advertisement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;

class AdvertisementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $advertisement = $builder->getData();
        $existing = $advertisement ? count($advertisement->getImages()) : 0;
        $remaining = max(0, 10 - $existing);

        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class, ['attr' => ['rows' => 7],])
            ->add('price')
            ->add('locationName', TextType::class, [
                'required' => false,
                'attr' => ['readonly' => true],
                ])
                ->add('latitude', HiddenType::class)
                ->add('longitude', HiddenType::class)
                ->add('imageFiles', FileType::class, [
                'label' => 'Photos (1-10)',
                'multiple' => true,
                'mapped' => false,
                'required' => $options['require_images'],
                'constraints' => [
                    new Count(
                        min: $options['require_images'] ? 1 : 0,
                        max: $remaining,
                        minMessage: 'Add at least one photo',
                        maxMessage: 'Maximum 10 photos',
                        exactMessage: 'This advertisement already has 10 photos',
                    ),
                    new All([
                        new File([
                            'maxSize' => '2M',
                            'extensions' => ['jpg', 'jpeg', 'png'],
                        ]),
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Advertisement::class,
            'require_images' => true,
        ]);
        $resolver->setAllowedTypes('require_images', 'bool');
    }
}

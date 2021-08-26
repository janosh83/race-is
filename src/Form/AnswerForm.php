<?php

namespace App\Form;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Translation\TranslatableMessage;

class AnswerForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('note', CKEditorType::class, ['required' => false, 'label' => new TranslatableMessage('Answer_note'), 'sanitize_html' => true, 'config' => ['toolbar' => 'standard']])
            ->add('image', FileType::class, [
                'label' => new TranslatableMessage('Picture'),

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '10240k',
                        'mimeTypes' => [
                            'application/jpg',
                            'application/jpeg',
                            'application/heic'
                        ],
                        'mimeTypesMessage' => new TranslatableMessage('Please_upload_valid_picture'),
                    ])
                ],
            ])
            ->add('save', SubmitType::class, ['label' => $options['form_label'] ])
            // ...
        ;

        if ($options['is_answered'])
        {
            $builder->add('delete', SubmitType::class, [
                'label' => new TranslatableMessage('Cancel_answer'), 
                'attr' => [ 'class' => 'btn-danger']]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
        ]);

        $resolver->setDefaults(['is_answered' => false]);
        $resolver->setDefaults(['form_label' => new TranslatableMessage('Canfirm_answer')]);

        // you can also define the allowed types, allowed values and
        // any other feature supported by the OptionsResolver component
        $resolver->setAllowedTypes('is_answered', 'bool');
        $resolver->setAllowedTypes('form_label', 'string');
    }
}
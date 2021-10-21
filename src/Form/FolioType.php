<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Folio;
use App\Entity\Item;

use Nines\MediaBundle\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Folio form.
 */
class FolioType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('pageNumber', null, [
            'label' => 'Page Number',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('status', TextType::class, [
            'label' => 'Status',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('text', TextareaType::class, [
            'label' => 'Text',
            'required' => false,
            'attr' => [
                'help_block' => '',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('hocr', TextareaType::class, [
            'label' => 'Hocr',
            'required' => false,
            'attr' => [
                'help_block' => '',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('item', Select2EntityType::class, [
            'label' => 'Item',
            'class' => Item::class,
            'remote_route' => 'item_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'item_new_popup',
                'add_label' => 'Add Item',
            ],
        ]);
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     */
    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Folio::class,
        ]);
    }
}

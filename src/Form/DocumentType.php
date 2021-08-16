<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Document;
use App\Entity\DocumentSet;

use Nines\DublinCoreBundle\Form\Mapper\DublinCoreMapper;
use Nines\DublinCoreBundle\Form\ValueType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\DataTransformer\EntityToPropertyTransformer;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Document form.
 */
class DocumentType extends ValueType {
    private DublinCoreMapper $mapper;

    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('documentSet', Select2EntityType::class, [
            'label' => 'Document Set',
            'class' => DocumentSet::class,
            'remote_route' => 'document_set_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'document_set_new_popup',
                'add_label' => 'Add Set',
            ],
            'transformer' => EntityToPropertyTransformer::class,
        ]);
        parent::buildForm($builder, $options);
        $builder->setDataMapper($this->mapper);
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     */
    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }

    /**
     * @required
     */
    public function setDublinCoreMapper(DublinCoreMapper $mapper) : void {
        $this->mapper = $mapper;
    }
}

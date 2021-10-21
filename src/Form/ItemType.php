<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Item;
use App\Entity\Scrapbook;

use Nines\DublinCoreBundle\Form\Mapper\DublinCoreMapper;
use Nines\DublinCoreBundle\Form\ValueType;
use Nines\DublinCoreBundle\Repository\ElementRepository;
use Nines\MediaBundle\Form\PdfType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Item form.
 */
class ItemType extends PdfType {
    private DublinCoreMapper $dcm;

    private ElementRepository $elementRepository;

    /**
     * Add form fields to $builder.
     *
     * @param null|mixed $label
     */
    public function buildForm(FormBuilderInterface $builder, array $options, $label = null) : void {
        parent::buildForm($builder, $options);
        $builder->add('scrapbook', Select2EntityType::class, [
            'label' => 'Scrapbook',
            'class' => Scrapbook::class,
            'remote_route' => 'scrapbook_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'scrapbook_new_popup',
                'add_label' => 'Add Scrapbook',
            ],
        ]);
        ValueType::add($builder, $options, $this->elementRepository);
        $builder->setDataMapper($this->dcm);
    }

    /**
     * @required
     */
    public function setElementRepository(ElementRepository $elementRepository) : void {
        $this->elementRepository = $elementRepository;
    }

    /**
     * @required
     */
    public function setDublinCoreMapper(DublinCoreMapper $dcm) : void {
        $this->dcm = $dcm;
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     */
    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }
}

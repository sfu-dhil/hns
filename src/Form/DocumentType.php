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

use App\Form\Mapper\DublinCoreMapper;
use Nines\DublinCoreBundle\Entity\Element;
use Nines\DublinCoreBundle\Repository\ElementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\DataTransformer\EntityToPropertyTransformer;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Document form.
 */
class DocumentType extends AbstractType {
    /**
     * @var ElementRepository
     */
    private ElementRepository $repo;
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

        foreach($this->repo->indexQuery()->execute() as $element) {
            /** @var Element $element */
            $builder->add($element->getName(), CollectionType::class, [
                'label' => $element->getLabel(),
                'entry_type' => TextType::class,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'entry_options' => [
                    'label' => false,
                ],
                'attr' => [
                    'help_block' => $element->getDescription(),
                    'class' => 'collection-simple',
                ],
                'mapped' => false,
            ]);
        }
        dump($builder->getDataMapper());
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
     * @param ElementRepository $repo
     * @required
     */
    public function setElementRepository(ElementRepository $repo) {
        $this->repo = $repo;
    }

    /**
     * @param DublinCoreMapper $mapper
     * @required
     */
    public function setDublinCoreMapper(DublinCoreMapper $mapper) {
        $this->mapper = $mapper;
    }

}

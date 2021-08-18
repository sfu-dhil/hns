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
use Nines\DublinCoreBundle\Repository\ElementRepository;
use Nines\MediaBundle\Form\LinkableType;
use Nines\MediaBundle\Form\Mapper\LinkableMapper;
use Nines\UtilBundle\Form\Mapper\SequentialMapper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\DataTransformer\EntityToPropertyTransformer;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Document form.
 */
class DocumentType extends AbstractType implements DataMapperInterface {
    private ElementRepository $repo;

    private DataMapperInterface $ppm;

    private DublinCoreMapper $dcm;

    private LinkableMapper $lm;

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
        ValueType::add($builder, $options, $this->repo);
        LinkableType::add($builder, $options);
        $this->ppm = $builder->getDataMapper();
        $builder->setDataMapper($this);
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
    public function setElementRepository(ElementRepository $repo) : void {
        $this->repo = $repo;
    }

    /**
     * @required
     */
    public function setMappers(DublinCoreMapper $dcm, LinkableMapper $lm) : void {
        $this->dcm = $dcm;
        $this->dcm->setParentCall(false);
        $this->lm = $lm;
        $this->lm->setParentCall(false);
    }

    public function mapDataToForms($viewData, $forms) : void {
        $mapper = new SequentialMapper($this->ppm, $this->dcm, $this->lm);
        $mapper->mapDataToForms($viewData, $forms);
    }

    public function mapFormsToData($forms, &$viewData) : void {
        $mapper = new SequentialMapper($this->ppm, $this->dcm, $this->lm);
        $mapper->mapFormsToData($viewData, $forms);
    }
}

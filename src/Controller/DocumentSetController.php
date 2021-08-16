<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\DocumentSet;
use Nines\DublinCoreBundle\Form\DocumentSetType;
use App\Repository\DocumentSetRepository;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/document_set")
 */
class DocumentSetController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="document_set_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, DocumentSetRepository $documentSetRepository) : array {
        $query = $documentSetRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'document_sets' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="document_set_search", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function search(Request $request, DocumentSetRepository $documentSetRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $documentSetRepository->searchQuery($q);
            $documentSets = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $documentSets = [];
        }

        return [
            'document_sets' => $documentSets,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="document_set_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, DocumentSetRepository $documentSetRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($documentSetRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="document_set_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $documentSet = new DocumentSet();
        $form = $this->createForm(DocumentSetType::class, $documentSet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($documentSet);
            $entityManager->flush();
            $this->addFlash('success', 'The new documentSet has been saved.');

            return $this->redirectToRoute('document_set_show', ['id' => $documentSet->getId()]);
        }

        return [
            'document_set' => $documentSet,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="document_set_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="document_set_show", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function show(DocumentSet $documentSet) {
        return [
            'document_set' => $documentSet,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="document_set_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, DocumentSet $documentSet) {
        $form = $this->createForm(DocumentSetType::class, $documentSet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated documentSet has been saved.');

            return $this->redirectToRoute('document_set_show', ['id' => $documentSet->getId()]);
        }

        return [
            'document_set' => $documentSet,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="document_set_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, DocumentSet $documentSet) {
        if ($this->isCsrfTokenValid('delete' . $documentSet->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($documentSet);
            $entityManager->flush();
            $this->addFlash('success', 'The documentSet has been deleted.');
        }

        return $this->redirectToRoute('document_set_index');
    }
}

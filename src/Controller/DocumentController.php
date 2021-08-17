<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\DublinCoreBundle\Repository\ElementRepository;
use Nines\MediaBundle\Controller\PdfControllerTrait;
use Nines\MediaBundle\Entity\Pdf;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/document")
 */
class DocumentController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    use PdfControllerTrait;

    /**
     * @Route("/", name="document_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, DocumentRepository $documentRepository) : array {
        $query = $documentRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'documents' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="document_search", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function search(Request $request, DocumentRepository $documentRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $documentRepository->searchQuery($q);
            $documents = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $documents = [];
        }

        return [
            'documents' => $documents,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="document_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, DocumentRepository $documentRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($documentRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="document_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($document);
            $entityManager->flush();
            $this->addFlash('success', 'The new document has been saved.');

            return $this->redirectToRoute('document_show', ['id' => $document->getId()]);
        }

        return [
            'document' => $document,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="document_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="document_show", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function show(Document $document, ElementRepository $repo) {
        return [
            'document' => $document,
            'elements' => $repo->indexQuery()->execute(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="document_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Document $document) {
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated document has been saved.');

            return $this->redirectToRoute('document_show', ['id' => $document->getId()]);
        }

        return [
            'document' => $document,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="document_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Document $document) {
        if ($this->isCsrfTokenValid('delete' . $document->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($document);
            $entityManager->flush();
            $this->addFlash('success', 'The document has been deleted.');
        }

        return $this->redirectToRoute('document_index');
    }

    /**
     * @Route("/{id}/new_pdf", name="document_new_pdf", methods={"GET", "POST"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @Template("@NinesMedia/pdf/new.html.twig")
     */
    public function newPdf(Request $request, Document $document) {
        return $this->newPdfAction($request, $document, 'document_show');
    }

    /**
     * @Route("/{id}/edit_pdf/{pdf_id}", name="document_edit_pdf", methods={"GET", "POST"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @ParamConverter("pdf", options={"id": "pdf_id"})
     *
     * @Template("@NinesMedia/pdf/edit.html.twig")
     */
    public function editPdf(Request $request, Document $document, Pdf $pdf) {
        return $this->editPdfAction($request, $document, $pdf, 'document_show');
    }

    /**
     * @Route("/{id}/delete_pdf/{pdf_id}", name="document_delete_pdf", methods={"DELETE"})
     * @ParamConverter("pdf", options={"id": "pdf_id"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     */
    public function deletePdf(Request $request, Document $document, Pdf $pdf) {
        return $this->deletePdfAction($request, $document, $pdf, 'document_show');
    }

}

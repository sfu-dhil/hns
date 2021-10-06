<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Scrapbook;
use App\Form\ScrapbookType;
use App\Repository\ScrapbookRepository;

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
 * @Route("/scrapbook")
 */
class ScrapbookController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="scrapbook_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, ScrapbookRepository $scrapbookRepository) : array {
        $query = $scrapbookRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'scrapbooks' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="scrapbook_search", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function search(Request $request, ScrapbookRepository $scrapbookRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $scrapbookRepository->searchQuery($q);
            $scrapbooks = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $scrapbooks = [];
        }

        return [
            'scrapbooks' => $scrapbooks,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="scrapbook_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, ScrapbookRepository $scrapbookRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($scrapbookRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="scrapbook_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $scrapbook = new Scrapbook();
        $form = $this->createForm(ScrapbookType::class, $scrapbook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($scrapbook);
            $entityManager->flush();
            $this->addFlash('success', 'The new scrapbook has been saved.');

            return $this->redirectToRoute('scrapbook_show', ['id' => $scrapbook->getId()]);
        }

        return [
            'scrapbook' => $scrapbook,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="scrapbook_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="scrapbook_show", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function show(Scrapbook $scrapbook) {
        return [
            'scrapbook' => $scrapbook,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="scrapbook_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Scrapbook $scrapbook) {
        $form = $this->createForm(ScrapbookType::class, $scrapbook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated scrapbook has been saved.');

            return $this->redirectToRoute('scrapbook_show', ['id' => $scrapbook->getId()]);
        }

        return [
            'scrapbook' => $scrapbook,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="scrapbook_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Scrapbook $scrapbook) {
        if ($this->isCsrfTokenValid('delete' . $scrapbook->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($scrapbook);
            $entityManager->flush();
            $this->addFlash('success', 'The scrapbook has been deleted.');
        }

        return $this->redirectToRoute('scrapbook_index');
    }
}

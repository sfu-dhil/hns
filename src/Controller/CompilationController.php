<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Compilation;
use App\Form\CompilationType;
use App\Repository\CompilationRepository;

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
 * @Route("/compilation")
 */
class CompilationController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="compilation_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, CompilationRepository $compilationRepository) : array {
        $query = $compilationRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'compilations' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="compilation_search", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function search(Request $request, CompilationRepository $compilationRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $compilationRepository->searchQuery($q);
            $compilations = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $compilations = [];
        }

        return [
            'compilations' => $compilations,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="compilation_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, CompilationRepository $compilationRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($compilationRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="compilation_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $compilation = new Compilation();
        $form = $this->createForm(CompilationType::class, $compilation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($compilation);
            $entityManager->flush();
            $this->addFlash('success', 'The new compilation has been saved.');

            return $this->redirectToRoute('compilation_show', ['id' => $compilation->getId()]);
        }

        return [
            'compilation' => $compilation,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="compilation_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="compilation_show", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function show(Compilation $compilation) {
        return [
            'compilation' => $compilation,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="compilation_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Compilation $compilation) {
        $form = $this->createForm(CompilationType::class, $compilation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated compilation has been saved.');

            return $this->redirectToRoute('compilation_show', ['id' => $compilation->getId()]);
        }

        return [
            'compilation' => $compilation,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="compilation_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Compilation $compilation) {
        if ($this->isCsrfTokenValid('delete' . $compilation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($compilation);
            $entityManager->flush();
            $this->addFlash('success', 'The compilation has been deleted.');
        }

        return $this->redirectToRoute('compilation_index');
    }
}

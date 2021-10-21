<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Folio;
use App\Form\FolioType;
use App\Repository\FolioRepository;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\MediaBundle\Controller\ImageControllerTrait;
use Nines\MediaBundle\Entity\Image;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/folio")
 */
class FolioController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;
    use ImageControllerTrait;

    /**
     * @Route("/", name="folio_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, FolioRepository $folioRepository) : array {
        $query = $folioRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'folios' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="folio_search", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function search(Request $request, FolioRepository $folioRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $folioRepository->searchQuery($q);
            $folios = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $folios = [];
        }

        return [
            'folios' => $folios,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="folio_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, FolioRepository $folioRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($folioRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="folio_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $folio = new Folio();
        $form = $this->createForm(FolioType::class, $folio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($folio);
            $entityManager->flush();
            $this->addFlash('success', 'The new folio has been saved.');

            return $this->redirectToRoute('folio_show', ['id' => $folio->getId()]);
        }

        return [
            'folio' => $folio,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="folio_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="folio_show", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function show(Folio $folio) {
        return [
            'folio' => $folio,
        ];
    }

    /**
     * @Route("/{id}/hocr", name="folio_hocr", methods={"GET"})
     * @Template
     *
     * @return Response
     */
    public function hocr(Folio $folio) {
        return new Response($folio->getHocr(), Response::HTTP_OK, ['content-type' => 'text/html']);
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="folio_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Folio $folio) {
        $form = $this->createForm(FolioType::class, $folio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated folio has been saved.');

            return $this->redirectToRoute('folio_show', ['id' => $folio->getId()]);
        }

        return [
            'folio' => $folio,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="folio_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Folio $folio) {
        if ($this->isCsrfTokenValid('delete' . $folio->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($folio);
            $entityManager->flush();
            $this->addFlash('success', 'The folio has been deleted.');
        }

        return $this->redirectToRoute('folio_index');
    }


    /**
     * @Route("/{id}/new_image", name="folio_new_image", methods={"GET", "POST"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @Template("folio/new_image.html.twig")
     */
    public function newImage(Request $request, Folio $folio) {
        return $this->newImageAction($request, $folio, 'folio_show');
    }

    /**
     * @Route("/{id}/edit_image/{image_id}", name="folio_edit_image", methods={"GET", "POST"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @ParamConverter("image", options={"id": "image_id"})
     *
     * @Template("folio/edit_image.html.twig")
     */
    public function editImage(Request $request, Folio $folio, Image $image) {
        return $this->editImageAction($request, $folio, $image, 'folio_show');
    }

    /**
     * @Route("/{id}/delete_image/{image_id}", name="folio_delete_image", methods={"DELETE"})
     * @ParamConverter("image", options={"id": "image_id"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     */
    public function deleteImage(Request $request, Folio $folio, Image $image) {
        return $this->deleteImageAction($request, $folio, $image, 'folio_show');
    }

}

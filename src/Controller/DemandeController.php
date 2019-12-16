<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Form\DemandeType;
use App\Repository\DemandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/demande")
 */
class DemandeController extends AbstractController {
	/**
	 * @Route("/", name="demande_index", methods={"GET"})
	 */
	public function indexAction(DemandeRepository $demandeRepository): Response {
		return $this->render('demande/index.html.twig', [
			'demandes' => $demandeRepository->findAll(),
		]);
	}

	/**
	 * @Route("/new", name="demande_new", methods={"GET","POST"})
	 */
	public function new (Request $request, \Swift_Mailer $mailer): Response{
		$demande = new Demande();
		$form = $this->createForm(DemandeType::class, $demande);
		$form->handleRequest($request);
		dump('coucou');
		if ($form->isSubmitted() && $form->isValid()) {
			$contactFormData = $form->getData();
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($demande);
			$entityManager->flush();

			$message = (new \Swift_Message('Nouvelle demande de réservation de ' . ' ' . $contactFormData->getNom() . ' ' . $contactFormData->getPrenom()))
				->setFrom($contactFormData->getEmail())
				->setTo('villadaisycorse@gmail.com')
				->setBody(
					'<html>' .
					' <body>' .
					'<p> Nouvelle demande de réservation de la part de : ' . $contactFormData->getNom() . ' ' . $contactFormData->getPrenom() . '. <br/>
				Pour le période du ' . date_format($contactFormData->getDateDebut(), "d M y") . '<br/>
				Au ' . date_format($contactFormData->getDateFin(), "d M y") . '<br/>
				</p>
				<p> Informations supplémentaire : <br/>
				Tel : 0' . $contactFormData->getTelephone() . '<br/>
				Adresse : ' . $contactFormData->getAdresse() . '<br/>
				Code Postal : ' . $contactFormData->getCp() . '<br/>
				Adresse Mail : ' . $contactFormData->getEmail() . '</p>' .
					' </body>' .
					'</html>',
					'text/html'
				);
			$mailer->send($message);
			$this->addFlash('success', 'Votre demande de réservation a bien été prise en compte !');

		}

		return $this->render('demande/new.html.twig', [
			'demande' => $demande,
			'email_form' => $form->createView(),
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/show/{id}", name="demande_show", methods={"GET"})
	 */
	public function show(Demande $demande): Response {
		return $this->render('demande/show.html.twig', [
			'demande' => $demande,
		]);
	}

	/**
	 * @Route("/{id}/edit", name="demande_edit", methods={"GET","POST"})
	 */
	public function edit(Request $request, Demande $demande): Response{
		$form = $this->createForm(DemandeType::class, $demande);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute('demande_index');
		}

		return $this->render('demande/edit.html.twig', [
			'demande' => $demande,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{id}", name="demande_delete", methods={"DELETE"})
	 */
	public function delete(Request $request, Demande $demande): Response {
		if ($this->isCsrfTokenValid('delete' . $demande->getId(), $request->request->get('_token'))) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove($demande);
			$entityManager->flush();
		}

		return $this->redirectToRoute('demande_index');
	}
}

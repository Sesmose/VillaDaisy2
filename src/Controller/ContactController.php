<?php
namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController {
	/**
	 * @Route("/contact",name="contact")
	 */
	public function index(request $request, \Swift_Mailer $mailer) {
		$form = $this->createForm(ContactType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$contactFormData = $form->getData();

			$message = (new \Swift_Message('Nouvelle demande de réservation de ' . ' ' . $contactFormData['Nom'] . ' ' . $contactFormData['Prenom'] . ' Plus de détails..'))
				->setFrom($contactFormData['Email'])
				->setTo('villadaisycorse@gmail.com')
				->setBody(
					$contactFormData['Message'] . '
            mail de l\'expéditeur: ' . $contactFormData['Email'] . ' et le reste ici.',
					'text/plain'
				);
			$mailer->send($message);
			//$this->addFlash('success', 'Une nouvelle demande d\'inscription a été envoyé');

			return $this->redirectToRoute('contact');
		}

		return $this->render('contact/index.html.twig', [
			'email_form' => $form->createView(),
		]);
	}

}
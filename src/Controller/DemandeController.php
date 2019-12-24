<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Demande;
use App\Form\DemandeType;
use App\Repository\BookingRepository;
use App\Repository\DemandeRepository;
use App\Service\CalendarService;
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
			if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
				$secret = '6Le08ccUAAAAAKO8xFICbArJbTfj2j7azcIo1Dyw';
				$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
				$responseData = json_decode($verifyResponse);
				if ($responseData->success) {
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
				Pour le période du  ' . date_format($contactFormData->getDateDebut(), "d M y") . '<br/>
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
					$this->addFlash('success', 'E-mail envoyé!!');
				} else {
					$this->addFlash(
						'warning',
						'Une Erreur est survenue avec le captcha, veuillez réessayer.'
					);
				}

			} else {
				$this->addFlash(
					'danger',
					'Captcha Requis'
				);
			}

		}
		/*if ($form->isSubmitted() && $form->isValid() && !$this->captchaverify($request->get('g-recaptcha-response'))) {

			$this->addFlash(
				'error',
				'Captcha Requis'
			);
		}*/

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
	 * @Route("/edit/{id}", name="demande_edit", methods={"GET","POST"})
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

	/**
	 * @Route("/accept/{id}", name="demande_accept")
	 */
	public function accept(Demande $demande, BookingRepository $bookingRepository) {
		if ($bookingRepository->findByBeginAt($demande->getDateDebut()) == NULL) {

			$demande->getId();
			$booking = new Booking;
			$service = CalendarService::service();

			$booking->setBeginAt($demande->getDateDebut());
			$booking->setEndAt($demande->getDateFin());
			$booking->setTitle($demande->getNom() . ' ' . $demande->getPrenom());
			$booking->setDescription('Réservation  ' . $demande->getNom() . ' ' . $demande->getPrenom() . ' 0' . $demande->getTelephone() . ' ' . $demande->getEmail());
			$booking->setDemande($demande);

			$entityManager = $this->getDoctrine()->getManager();

			$entityManager->persist($booking);
			$calendarId = 'primary';

			//Création d'un nouvel event et définition des valeurs attendues par la base de donnée de Google
			$event = new \Google_Service_Calendar_Event([
				'summary' => $booking->getTitle(),
				'description' => $booking->getDescription(),
				//Formatage de la date pour le bon fuseau horraire.
				'start' => ['dateTime' => date_format($booking->getBeginAt(), "Y-m-d\TH:i:s"),
					'timeZone' => 'Europe/Paris'],
				'end' => ['dateTime' => date_format($booking->getEndAt(), "Y-m-d\TH:i:s"),
					'timeZone' => 'Europe/Paris'],
			]);
			//création d'évenement du coté de google
			$results = $service->events->insert($calendarId, $event);
			//écriture en bdd de l'id de cet évènement google pour pouvoir le modifier par la suite
			$booking->setGoogleid($results->getId());

			$entityManager->flush();

		} else {

			$this->addFlash(
				'danger',
				'Il y a déjà une réservation commençant le ' . date_format($demande->getDateDebut(), "d-m-Y"),
			);

		}

		return $this->redirectToRoute('booking_index');
	}

}

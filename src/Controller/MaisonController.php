<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class MaisonController {

	/**
	 * @var Environment
	 */
	private $twig;

	public function __construct(Environment $twig) {
		$this->twig = $twig;
	}

	/**
	 * @Route("/maison", name="maison", methods={"GET","POST"})
	 */
	public function index(): Response {
		return new Response($this->twig->render('pages/maison.html.twig'));
	}
}
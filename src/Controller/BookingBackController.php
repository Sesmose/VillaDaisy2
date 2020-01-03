<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Demande;
use App\Service\CalendarService;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use EasyCorp\Bundle\EasyAdminBundle\Exception\EntityRemoveException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class BookingBackController extends EasyAdminController {
	/**
	 * The method that is executed when the user performs a 'new' action on an entity.
	 * @return Response|RedirectResponse
	 */
	protected function newAction() {
		$this->dispatch(EasyAdminEvents::PRE_NEW);
		$entity = $this->executeDynamicMethod('createNew<EntityName>Entity');
		$easyadmin = $this->request->attributes->get('easyadmin');
		$easyadmin['item'] = $entity;
		$this->request->attributes->set('easyadmin', $easyadmin);
		$fields = $this->entity['new']['fields'];
		$newForm = $this->executeDynamicMethod('create<EntityName>NewForm', [$entity, $fields]);
		$newForm->handleRequest($this->request);

		//Appel du service créé pour interragir avec la BDD google calendar
		$service = CalendarService::service();
		if ($newForm->isSubmitted() && $newForm->isValid()) {
			if (strtotime(date_format($entity->getBeginAt(), 'd-m-Y H:i:s')) < time()) {
				$this->addFlash('error', 'La date de début de l\'évènement ' . date_format($entity->getBeginAt(), 'd/m/Y H:i') . ' ne peut être antérieur à aujourd\'hui !');
				return $this->redirectToRoute('easyadmin', array(
					'action' => 'new',
					'entity' => $this->request->query->get('entity'),
				));
			}
			$class = $this->entity['class'];
			$em = $this->getDoctrine()->getManagerForClass($class);
			$ids = $this->getDoctrine()->getRepository(Booking::class)->findAll();
			foreach ($ids as $id) {
				$event = $em->find($class, $id);
				if ($entity != $event && $entity->getBeginAt() == $event->getBeginAt()) {
					$this->addFlash('error', 'L\'évènement ' . $event->getTitle() . ' existe déjà  !');
					return $this->redirectToRoute('easyadmin', array(
						'action' => 'new',
						'entity' => $this->request->query->get('entity'),
					));
				}
			}

			//définition du calendar ID attendu par google
			$calendarId = 'primary';
			//On rentre les données attendue avec celle de notre entity
			$gevent = new \Google_Service_Calendar_Event([
				'summary' => $entity->getTitle(),
				'description' => $entity->getDescription(),
				//Formatage de la date pour le bon fuseau horraire.
				'start' => ['dateTime' => date_format($entity->getBeginAt(), "Y-m-d\TH:i:s"),
					'timeZone' => 'Europe/Paris'],
				'end' => ['dateTime' => date_format($entity->getEndAt(), "Y-m-d\TH:i:s"),
					'timeZone' => 'Europe/Paris'],
			]);
			//création d'évenement du coté de google
			$results = $service->events->insert($calendarId, $gevent);
			//écriture en bdd de l'id de cet évènement google pour pouvoir le modifier par la suite
			$entity->setGoogleid($results->getId());

			$this->addFlash('success', 'L\'évènement ' . $entity->getTitle() . ' a été ajouté avec succès !');

			$this->processUploadedFiles($newForm);
			$this->dispatch(EasyAdminEvents::PRE_PERSIST, ['entity' => $entity]);
			$this->executeDynamicMethod('persist<EntityName>Entity', [$entity, $newForm]);
			$this->dispatch(EasyAdminEvents::POST_PERSIST, ['entity' => $entity]);

			return $this->redirectToReferrer();
		}
		$this->dispatch(EasyAdminEvents::POST_NEW, [
			'entity_fields' => $fields,
			'form' => $newForm,
			'entity' => $entity,
		]);
		$parameters = [
			'form' => $newForm->createView(),
			'entity_fields' => $fields,
			'entity' => $entity,
		];
		return $this->executeDynamicMethod('render<EntityName>Template', ['new', $this->entity['templates']['new'], $parameters]);
	}

	/**
	 * The method that is executed when the user performs a 'edit' action on an entity.
	 *
	 * @return Response|RedirectResponse
	 *
	 * @throws \RuntimeException
	 */
	protected function editAction() {
		$this->dispatch(EasyAdminEvents::PRE_EDIT);

		$id = $this->request->query->get('id');
		$easyadmin = $this->request->attributes->get('easyadmin');
		$entity = $easyadmin['item'];

		if ($this->request->isXmlHttpRequest() && $property = $this->request->query->get('property')) {
			$newValue = 'true' === mb_strtolower($this->request->query->get('newValue'));
			$fieldsMetadata = $this->entity['list']['fields'];

			if (!isset($fieldsMetadata[$property]) || 'toggle' !== $fieldsMetadata[$property]['dataType']) {
				throw new \RuntimeException(sprintf('The type of the "%s" property is not "toggle".', $property));
			}

			$this->updateEntityProperty($entity, $property, $newValue);

			// cast to integer instead of string to avoid sending empty responses for 'false'
			return new Response((int) $newValue);
		}

		$fields = $this->entity['edit']['fields'];

		$editForm = $this->executeDynamicMethod('create<EntityName>EditForm', [$entity, $fields]);
		$deleteForm = $this->createDeleteForm($this->entity['name'], $id);

		$editForm->handleRequest($this->request);
		if ($editForm->isSubmitted() && $editForm->isValid()) {
			if ($entity instanceof Booking) {
				$class = $this->entity['class'];
				$em = $this->getDoctrine()->getManagerForClass($class);
				$ids = $this->getDoctrine()->getRepository(Booking::class)->findAll();


				$service = CalendarService::service();

				$gevent = $service->events->get('primary', $entity->getGoogleid());

				$gevent = new \Google_Service_Calendar_Event([
					'summary' => $entity->getTitle(),
					'description' => $entity->getDescription(),
					'start' => ['dateTime' => date_format($entity->getBeginAt(), "Y-m-d\TH:i:s"),
						'timeZone' => 'Europe/Paris'],
					'end' => ['dateTime' => date_format($entity->getEndAt(), "Y-m-d\TH:i:s"),
						'timeZone' => 'Europe/Paris'],
				]);

				$updatedEvent = $service->events->update('primary', $entity->getGoogleid(), $gevent);
					$this->addFlash('success', 'L\'évènement ' . $entity->getTitle() . ' a été modifié avec succcès !');


				
			}
			$this->processUploadedFiles($editForm);
			$this->dispatch(EasyAdminEvents::PRE_UPDATE, ['entity' => $entity]);
			$this->executeDynamicMethod('update<EntityName>Entity', [$entity, $editForm]);
			$this->dispatch(EasyAdminEvents::POST_UPDATE, ['entity' => $entity]);

			return $this->redirectToReferrer();
		}

		$this->dispatch(EasyAdminEvents::POST_EDIT);

		$parameters = [
			'form' => $editForm->createView(),
			'entity_fields' => $fields,
			'entity' => $entity,
			'delete_form' => $deleteForm->createView(),
		];

		return $this->executeDynamicMethod('render<EntityName>Template', ['edit', $this->entity['templates']['edit'], $parameters]);
	}

	protected function deleteAction() {
		$this->dispatch(EasyAdminEvents::PRE_DELETE);

		if ('DELETE' !== $this->request->getMethod()) {
			return $this->redirect($this->generateUrl('easyadmin', ['action' => 'list', 'entity' => $this->entity['name']]));
		}

		$id = $this->request->query->get('id');
		$form = $this->createDeleteForm($this->entity['name'], $id);
		$form->handleRequest($this->request);

		if ($form->isSubmitted() && $form->isValid()) {
			$easyadmin = $this->request->attributes->get('easyadmin');
			$entity = $easyadmin['item'];

			$service = CalendarService::service();
			$service->events->delete('primary', $entity->getGoogleid());

			$this->dispatch(EasyAdminEvents::PRE_REMOVE, ['entity' => $entity]);
		
			}

			try {
				$this->executeDynamicMethod('remove<EntityName>Entity', [$entity, $form]);
			} catch (ForeignKeyConstraintViolationException $e) {
				throw new EntityRemoveException(['entity_name' => $this->entity['name'], 'message' => $e->getMessage()]);
			}
			$this->addFlash('success', 'L\'évènement ' . $entity->getTitle() . ' a été supprimé avec succès !');
			$this->dispatch(EasyAdminEvents::POST_REMOVE, ['entity' => $entity]);
		

		$this->dispatch(EasyAdminEvents::POST_DELETE);

		return $this->redirectToReferrer();
	}
	protected function deleteBatchAction(array $ids): void{
		$class = $this->entity['class'];
		$primaryKey = $this->entity['primary_key_field_name'];


		$entities = $this->em->getRepository($class)
			->findBy([$primaryKey => $ids]);

		foreach ($entities as $entity){
			$this->em->remove($entity);
			$service = CalendarService::service();
			$service->events->delete('primary', $entity->getGoogleid());
		}

		$this->addFlash('success', 'Toutes les réservations sélectionnées ont été supprimés avec succès !');
		$this->em->flush();
	}
	

}
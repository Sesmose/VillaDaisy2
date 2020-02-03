<?php

namespace App\Form;

use App\Entity\Demande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('date_debut', DateType::class, [
				'widget' => 'single_text',

				'html5' => false,

				'attr' => ['class' => 'js-datepicker'],
				'attr'=>['autocomplete' => 'off']
			])
			->add('date_fin', DateType::class, [
				'widget' => 'single_text',

				'html5' => false,

				'attr' => ['class' => 'js-datepicker'],
				'attr'=>['autocomplete' => 'off']
			])
			->add('nom')
			->add('prenom')
			->add('email')
			->add('telephone')
			->add('adresse')
			->add('ville')
			->add('cp')
			//->add('created_at')
			//->add('updated_at')
		;
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'data_class' => Demande::class,
		]);
	}
}

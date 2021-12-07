<?php 

namespace App\Notification;

use App\Entity\Contact;
use App\Entity\User;
use Twig\Environment;

class ContactNotification
{
	
	/**
 	* @var \Swift_Mailer 
	*/
	private $mailer;

	/**
 	* @var Environment
	*/
	private $renderer;

	function __construct(\Swift_Mailer $mailer, Environment $renderer)
	{
		$this->mailer = $mailer;
		$this->renderer = $renderer;
		// hors d'un cntroller il est possible de faire des injections de dépendance SEULEMENT dans un constructeur
	}

	public function notify(Contact $contact)
	{
		$message = (new \Swift_Message('Nouveau message de ' . $contact->getEmail())) //sujet du mail
				->setFrom($contact->getEmail()) // adresse mail de l'expéditeur
				->setTo('samuel.evgform@gmail.com') // adresse mail du destinataire
				->setReplyTo($contact->getEmail()) // adresse mail de reponse
				->setBody($this->renderer->render('emails/contact.html.twig', [
					'contact' =>$contact
				]),
					'text/html'); // contenu du mail, corps du message déclaréda,s le template emails/contact.html.twig				
		$this->mailer->send($message);
	}

	// Méthode qui permettra d'envoyer un email à l'utilisateur avec un message de bienvenue qui confirme son inscription 
	public function registerNotify(User $user) // Cette méthode sera appelée dans 
	{
		$message = (new \Swift_Message('Nouveau message de ' . $user->getEmail())) //sujet du mail
				->setFrom($user->getEmail()) // adresse mail de l'expéditeur
				->setTo('samuel.evgform@gmail.com') // adresse mail du destinataire
				->setReplyTo($user->getEmail()) // adresse mail de reponse
				->setBody($this->renderer->render('emails/register.html.twig', [
					'user' =>$user
				]),
					'text/html'); // contenu du mail, corps du message déclaréda,s le template emails/contact.html.twig				
		$this->mailer->send($message);
	}




}
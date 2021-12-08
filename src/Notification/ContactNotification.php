<?php 

namespace App\Notification;

// Liste des tables que l'on va utiliser
use App\Entity\Contact;
use App\Entity\User;
// Liste des classe prevenant de Twig
use Twig\Environment;

// Classe ContactNotification qui est en dehors du controller de l'application
class ContactNotification
{
	// Liste des propriétes de la classe ContactNotification
	/**
 	* @var \Swift_Mailer 
	*/
	private $mailer;

	/**
 	* @var Environment
	*/
	private $renderer;

	// Comme on est en dehors d'un controller il faut un construteur pour pouvoir faire des injections de dépendance
	function __construct(\Swift_Mailer $mailer, Environment $renderer)
	{
		$this->mailer = $mailer;
		$this->renderer = $renderer;
	}

	// Méthode qui permettra à l'utilisateur d'envoyer un mail 
	public function notify(Contact $contact)
	{
		// On stock dans $message les méthodes propre a Swift Mailer
		$message = (new \Swift_Message('Nouveau message de ' . $contact->getEmail())) //sujet du mail
				->setFrom($contact->getEmail()) // adresse mail de l'expéditeur
				->setTo('samuel.evgform@gmail.com') // adresse mail du destinataire
				->setReplyTo($contact->getEmail()) // adresse mail de reponse
				// On va afficher dans body la propriété renderer en aplliquant la méthode render()
				->setBody($this->renderer->render('emails/contact.html.twig', [
					'contact' =>$contact
				]),
					'text/html'); // contenu du mail dans le corps du message déclaré dans le template emails/contact.html.twig	
		// On applique la méthode send() en fonction de $message 
		$this->mailer->send($message);
	}

	// Méthode qui permettra d'envoyer un email à l'utilisateur avec un message de bienvenue qui confirme son inscription 
	public function registerNotify(User $user) // Cette méthode sera appelée dans 
	{
		$message = (new \Swift_Message('Nouveau message de ' . $user->getEmail())) //sujet du mail
				->setFrom($user->getEmail()) // adresse mail de l'expéditeur
				->setTo('samuel.evgform@gmail.com') // adresse mail du destinataire
				->setReplyTo($user->getEmail()) // adresse mail de reponse
				// On va afficher dans body la propriété renderer en aplliquant la méthode render()
				->setBody($this->renderer->render('emails/register.html.twig', [
					'user' =>$user
				]),
					'text/html'); // contenu du mail dans le corps du message déclaré dans le template emails/register.html.twig
		// On applique la méthode send() en fonction de $message 
		$this->mailer->send($message);
	}

}
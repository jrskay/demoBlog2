<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Notification\ContactNotification;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager, ContactNotification $notification): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // Envoyer un mail de confirmation d'inscription à l'utilisateur (la méthode déclarer dans le fichier /src/Notification/ContactNotification.php)
            $notification->registerNotify($user);
            // Affichage d'un message après la création de compte, sur la page de connexion
            $this->addFlash('registerSuccess', 'Votre compte a bien été crée, vous pouvez maintenant vous connecter !'); // 1er paramètre variable registerSuccess et 2ème paramètre sa valeur

            // Redirection sur la page de connexion qui est le fichier /templates/security/login.html.twig
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}

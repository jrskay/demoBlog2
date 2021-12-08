<?php

namespace App\Controller;

// Liste des tables que l'on va utiliser
use App\Entity\User;
// Liste des formulaires que l'on va utiliser
use App\Form\RegistrationFormType;
// Liste des fonctionnalités que l'on a créé en dehors des controlleurs de l'application 
use App\Notification\ContactNotification;
// Pour les manipulations en BDD
use Doctrine\ORM\EntityManagerInterface;
// Liste de classes provenant de Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

// Controller de l'inscription
class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    // Méthode qui permettra de s'inscrire à l'application
    public function register(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager, ContactNotification $notification): Response
    {
        // On instancie un nouvel utilisateur
        $user = new User();
        // On stocke dans $form cette méthode createForm() avec en paramètre les paramètres formulaire de user avec l'objet instancié précédemment $user 
        $form = $this->createForm(RegistrationFormType::class, $user);
        // On applique la méthode handleRequest() pour faire la requete
        $form->handleRequest($request);
        // Si le formulaire est soumit et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            // On va chiffré le mot de passe saisie par l'utilisateur
            $user->setPassword(
            $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // On prépare l'insertion des informations
            $entityManager->persist($user);
            // On lance en Bdd les informations 
            $entityManager->flush();
            // On envoi un mail de confirmation d'inscription à l'utilisateur (la méthode déclarer dans le fichier /src/Notification/ContactNotification.php)
            $notification->registerNotify($user);
            // Affichage d'un message après la création de compte, sur la page de connexion
            $this->addFlash('registerSuccess', 'Votre compte a bien été crée, vous pouvez maintenant vous connecter !'); // 1er paramètre clé registerSuccess et 2ème paramètre sa valeur qui est en chaine de caractère

            // Redirection sur la page de connexion qui est le fichier /templates/security/login.html.twig
            // On retourne cette méthode redirection vers la route cela permet de recharger la page et vider le formulaire
            return $this->redirectToRoute('app_login');
        }
        // On retourne la méthode affichage render() 1er paramètre chemin du fichier template 2ème paramètre tableaux des variables avec leur valeur qui seront utiliseés dans la page
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}

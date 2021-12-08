<?php

namespace App\Controller;

// Liste des tables que l'on va utiliser
use App\Entity\Article;
use App\Entity\Contact;
// Liste des formulaires que l'on va utiliser
use App\Form\ContactType;
use App\Form\ArticleType;
// Liste des répétoires de BDD que l'on va utiliser
use App\Repository\ArticleRepository;
// Liste des fonctionnalités que l'on a créé en dehors des controlleurs de l'application 
use App\Notification\ContactNotification;
// Pour les manipulations en BDD
use Doctrine\ORM\EntityManagerInterface;
// Liste de classes provenant de Symfony
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Controller du Blog
class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    // Méthode qui affichera tous les articles dans la page /blog
    public function index(ArticleRepository $repo): Response
    {
        // On stocke dans $articles la réponse venant du répertoire Article en applicant la méthode findAll()
        $articles = $repo->findAll();
        // On retourne la méthode affichage render() 1er paramètre chemin du fichier template 2ème paramètre tableaux des variables avec leur valeur qui seront utiliseés dans la page
        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/blog/show/{id}", name="blog_show")
     */
    // Méthode qui affichera l'article dans la page /blog/show/id
    public function show(Article $article = null)
    // On met en paramètre par défaut $article = null si l'id de l'article n'existe pas, $article = null 
    {
        // S'il y a pas d'article c.a.d null 
        if(!$article)
        {
            // On retourne cette méthode d'affichage avec le chemin du fichier template 
            return $this->render("error/404.html.twig");
        }
        // On retourne la méthode affichage render() 1er paramètre chemin du fichier template 2ème paramètre tableaux des variables avec leur valeur qui seront utiliseés dans la page
        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     *@Route("/blog/edit/{id}", name="blog_edit")
     */
    // Méthode qui affichera le formulaire ajout ou de modification d'article dans la page l'article /blog/new ou /blog/edit/id 
    public function form(Request $request, EntityManagerInterface $manager, Article $article = null)
    {
        // S'il y a pas d'article c.a.d null
        if(!$article)
        {
            // On instancie un nouvel article
            $article = new Article;
            // On applique à l'objet la méthode setCreatedAt() de la classe Article  avec le format DateTime()
            $article->setCreatedAt(new \DateTime());
        }
        // On stocke dans $form cette méthode createForm() avec en paramètre les paramètres formulaire de article avec l'objet instancié précédemment $article 
        $form = $this->createForm(ArticleType::class, $article);
        // On applique la méthode handleRequest() pour faire la requete
        $form->handleRequest($request);
        // Si le formulaire est soumit et valide
        if($form->isSubmitted() && $form->isValid())
        {
            // On prépare l'insertion des informations 
            $manager->persist($article);
            // On lance en Bdd les informations 
            $manager->flush();
            // On retourne cette méthode redirection vers la route 1er paramètre le name de la route de destination et 2ème paramètre un tableau des variables et leurs valeurs qui seront utiliser dans cette route
            return $this->redirectToRoute("blog_show",[
                'id' => $article->getId()
            ]);
        }
        // On retourne la méthode affichage render() 1er paramètre chemin du fichier template 2ème paramètre tableaux des variables avec leur valeur qui seront utiliseés dans la page
        return $this->render("blog/form.html.twig", [
            'articleForm' => $form->createView(),
            'editMode' => $article->getId() !== NULL
        ]);
    }

    /**
     * @Route("/blog/contact", name="blog_contact")
     */
    // Méthode qui permettra à l'utilisateur d'envoyer un mail
    public function contact(EntityManagerInterface $manager, Request $request, ContactNotification $notification)
    {
        // On instancie un nouvel contact
        $contact = new Contact;
        // On stocke dans $form cette méthode createForm() avec en paramètre les paramètres formulaire de contact avec l'objet instancié précédemment $contact 
        $form = $this->createForm(ContactType::class, $contact);
        // On applique la méthode handleRequest() pour faire la requete
        $form->handleRequest($request);
        // Si le formulaire est soumit et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On prépare l'insertion des informations 
            $manager->persist($contact);
            // On lance en Bdd les informations 
            $manager->flush();
            // On applique la méthode notify déclarer dans la ContactNotification en fonction du contact
            $notification->notify($contact);
            // addFlash est une méthode qui permet de stocker des messages (dans une liste flashes) de notification pour les afficher dans une vue afin d'avertir l'utilisateur
            $this->addFlash('success', 'Votre email a bien été envoyé !');// 1er paramètre clé success et 2ème paramètre sa valeur qui est en chaine de caractère

            // On retourne cette méthode redirection vers la route cela permet de recharger la page et vider le formulaire
            return $this->redirectToRoute('blog_contact');
        }
        // On retourne la méthode affichage render() 1er paramètre chemin du fichier template 2ème paramètre tableaux des variables avec leur valeur qui seront utiliseés dans la page
        return $this->render("blog/contact.html.twig",[
            'formContact' => $form->createView()
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * Affiche la page de connexion.
     *
     * Si l'utilisateur est déjà connecté, il est redirigé vers la page d'accueil. 
     * Si l'utilisateur n'est pas connecté, il est présenté avec un formulaire de connexion
     *
     * Les paramètres sont (last_username: le dernier nom d'utilisateur saisi, error: une erreur d'authentification s'il y en a).
     *
     * @param AuthenticationUtils $authenticationUtils : Fournit les informations sur l'état de l'authentification (erreurs, dernier nom d'utilisateur).
     * @return Response La réponse contenant la page de connexion.
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
             'error' => $error,
             'cartCount'=>0
            ]);
    }

    /**
     * Gère la déconnexion de l'utilisateur.
     *
     * Cette méthode est utilisée pour intercepter la déconnexion via la configuration du firewall.
     * Symfony gère automatiquement la déconnexion.
     *
     * @return void
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

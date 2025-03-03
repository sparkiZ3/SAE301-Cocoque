<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

use App\Repository\UserRepository;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    /**
     * Permet à un utilisateur de créer un compte.
     *
     * Cette méthode permet de traiter la soumission du formulaire d'inscription d'un utilisateur 
     * et vérifie au préalable si l'utilisateur est déjà connecté et le redirige vers la page d'accueil si c'est le cas.
     * Ensuite, elle traite le formulaire, effectue les vérifications nécessaires (comme le respects de la syntaxe de l'email => ****@****.***), 
     * et enregistre l'utilisateur dans la base de données si tout est valide. L'utilisateur est connecté automatiquement après la confirmation
     * de son inscription.
     *
     *
     * @param Request $request La requête HTTP contenant les données du formulaire d'inscription.
     * @param UserPasswordHasherInterface $userPasswordHasher Service permettant de hacher le mot de passe de l'utilisateur.
     * @param Security $security Service de sécurité pour effectuer l'authentification de l'utilisateur après l'enregistrement.
     * @param EntityManagerInterface $entityManager Service d'entité pour persister les données de l'utilisateur dans la base de données.
     * @param UserRepository $userRepo Le repository qui permet d'interroger les utilisateurs existants dans la base de données et ajouter l'utilisateur
     *
     * @return Response La réponse renvoie la vue d'enregistrement si le formulaire est soumis, ou la redirection si l'utilisateur est déjà connecté.
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, UserRepository $userRepo): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            if($userRepo->findUserByEmail($user->getEmail())){
                $errorEmail = new FormError('email deja existant');
                $form->addError( $errorEmail);
                return $this->render('security/register.html.twig', [
                    'registrationForm' => $form,
                    'cartCount'=>0,
                ]); 
            }

            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setVerified(false);
            $user->setUsername($user->getFirstName()."_".$user->getLastName());

            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('support@cocoque.fr', 'Support'))
                    ->to((string) $user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('security/confirmation_email.html.twig')
            );


            return $security->login($user, AppAuthenticator::class, 'main');
        }


        return $this->render('security/register.html.twig', [
            'registrationForm' => $form,
            'cartCount'=>0,
        ]);
    }

    /**
     * 
     * 
     * Vérifie l'adresse email de l'utilisateur après la confirmation du lien d'activation.
     *
     * Cette méthode est appelée lorsqu'un utilisateur clique sur le lien de confirmation 
     * envoyé par email. Elle vérifie le lien de confirmation, active l'adresse email de l'utilisateur 
     * (en définissant `User::isVerified` à `true`), et persiste cette information dans la base de données.
     * Si une erreur survient (par exemple un lien expiré ou invalide), un message d'erreur est affiché.
     *
     * Attention : Ne fonctionne pas pour l'instant
     *
     * @param Request $request La requête HTTP contenant les informations de validation du lien de confirmation.
     * @param TranslatorInterface $translator Le service de traduction pour afficher un message d'erreur localisé si nécessaire.
     *
     * @return Response La réponse redirige l'utilisateur vers la page d'inscription, avec un message de succès ou d'erreur.
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre email a été vérifié. ');

        return $this->redirectToRoute('app_register');
    }
}

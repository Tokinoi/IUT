<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;

class SecurityController extends AbstractController
{
    /**
     * Creates a new user account.
     *
     * This endpoint allows users to register by creating a new account. To register, make a POST request to '/api/create-user'.
     * You must provide the following data:
     * {
     *     "email",
     *     "password",
     *     "firstname",
     *     "lastname"
     * }
     *
     * If any of the fields are empty, an error message will be returned for display:
     * {
     *     "error": "Tous les champs doivent être remplis."
     * }
     *
     * If the email format is incorrect, an error message will be returned for display:
     * {
     *     "error": "Format de l'adresse e-mail incorrect."
     * }
     *
     * If the password length is less than 8 characters, an error message will be returned for display:
     * {
     *     "error": "Le mot de passe doit contenir au moins 8 caractères."
     * }
     *
     * If a duplicate account exists, an error message will be returned for display:
     * {
     *     "error": "Un compte avec cet e-mail existe déjà."
     * }
     *
     * If the account is successfully created, a success message will be returned along with a redirection to the login page:
     * {
     *     "message": {
     *         "type": "success",
     *         "content": "Compte créé avec succès !"
     *     },
     *     "redirect_to": "/login"
     * }
     *
     * @Route("/api/create-user", methods={"POST"})
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the a success/error message',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['full']))
        )
    )]
    #[OA\Parameter(
        name: 'email',
        description: 'The email of the user to be created',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'password',
        description: 'The password of the user to be created',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'firstname',
        description: 'The firstname of the user to be created',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'lastname',
        description: 'The lastname of the user to be created',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Account')]
    #[Route('/api/create-user', name: 'createUser', methods: ['POST'])]
    public function createUser(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse {
        $firstname = $request->request->get('firstname');
        $lastname = $request->request->get('lastname');
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        // Checks if all fields have a value
        if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
            return $this->json(['error' => 'Tous les champs doivent être remplis.'], 400);
        }

        // Email verification
        $emailConstraint = new Email();
        $emailErrors = $validator->validate($email, $emailConstraint);
        if (count($emailErrors) > 0) {
            return $this->json(['error' => 'Format de l\'adresse e-mail incorrect.'], 400);
        }

        // Password length verification
        if (strlen($password) < 8) {
            return $this->json(['error' => 'Le mot de passe doit contenir au moins 8 caractères.'], 400);
        }

        // Verify duplicate account
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return $this->json(['error' => 'Un compte avec cet e-mail existe déjà.'], 400);
        }

        // Creates the user and persists him to the DB
        $user = new User();
        $user->setLastname($lastname);
        $user->setFirstname($firstname);
        $user->setEmail($email);
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            [
                'message' => [
                    'type' => 'success',
                    'content' => 'Compte créé avec succès !'
                ],
                'redirect_to' => '/login'
            ],
            201
        );
    }

    /**
     * Sends a reset key via email for password reset.
     *
     * This endpoint allows users to request a password reset by sending a reset key via email.
     * To request a password reset, make a POST request to '/api/send-resetKey' with the email provided as data.
     *
     * If the provided email corresponds to an existing account, a reset key will be generated and sent via email.
     * The reset key will be valid for one hour.
     *
     * @Route("/api/send-resetKey", methods={"POST"})
     */
    #[OA\Response(
        response: 200,
        description: 'Returns a success message if the reset key is successfully sent'
    )]
    #[OA\Parameter(
        name: 'email',
        description: 'The email of the user to send the reset key',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Password reset')]
    #[Route('/api/send-resetKey', name: 'sendResetKey', methods: ['POST'])]
    public function sendResetKey(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        MailerInterface $mailer,
        BodyRendererInterface $bodyRenderer,
    ): JsonResponse {
        // Tries to find the user
        $email = $request->request->get('email');
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            // Do nothing
            return $this->json([]);
        }

        // Sets the reset key and its expiration date
        $user->setResetKey(uniqid());
        $user->setExpiresAt(new \DateTime('+1 hour'));

        $entityManager->flush();

        // Sends the mail with the reset key
        $email = (new TemplatedEmail())
            ->from('noreply@voyo.fr')
            ->to($user->getEmail())
            ->subject('Réinitialisation du mot de passe - Voyo')
            ->htmlTemplate('emails/resetPassword.html.twig')
            ->context(['resetKey' => $user->getResetKey()]);
        $bodyRenderer->render($email);
        $mailer->send($email);

        return $this->json(['message' => 'Lien de réinitialisation envoyé par e-mail.']);
    }

    /**
     * Resets the user's password using the provided reset key or current user's credentials.
     *
     * This endpoint allows users to reset their passwords using either the reset key sent via email or their current credentials.
     * To reset the password using the reset key, provide the reset key and the new password as data to '/api/reset-password' endpoint via POST request.
     * To reset the password using the current user's credentials, ensure the user is authenticated and provide the new password as data.
     *
     * If the reset key is provided, it will be used to verify the reset process. If not, the current user's credentials will be used.
     * The reset key will be considered invalid or expired if the provided reset key does not match or if it has expired.
     *
     * @Route("/api/reset-password", methods={"POST"})
     */
    #[OA\Response(
        response: 200,
        description: 'Returns a success message if the password is successfully reset',
    )]
    #[OA\Parameter(
        name: 'resetKey',
        description: 'The reset key sent via email',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'newPassword',
        description: 'The new password to set',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Password reset')]
    #[Route('/api/reset-password', name: 'resetPassword', methods: ['POST'])]
    public function resetPassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): JsonResponse
    {
        $resetKey = $request->request->get('resetKey');
        $newPassword = $request->request->get('newPassword');

        // Checks if a user is connected to determinate if a resetKey is needed to reset the password
        if ($this->getUser()) {
            $user = $userRepository->find($this->getUser()->getId());
            // Password length verification
            if (strlen($newPassword) < 8) {
                return $this->json(['error' => 'Le mot de passe doit contenir au moins 8 caractères.'], 400);
            }

            // Hashes the password and sets it
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            $entityManager->flush();

            return $this->json(
                [
                    'message' => [
                        'type' => 'success',
                        'content' => 'Mot de passe modifié avec succès !'
                    ],
                    'redirect_to' => '/userProfile'
                ],
                201
            );
        } else {
            // Tries to find the user using the provided reset key
            $user = $userRepository->findOneBy(['resetKey' => $resetKey]);

            // Checks if the reset key expired
            if (!$user || $user->getExpiresAt() < new \DateTime()) {
                return $this->json(['error' => 'Token de réinitialisation invalide ou expiré.'], 400);
            }

            // Password length verification
            if (strlen($newPassword) < 8) {
                return $this->json(['error' => 'Le mot de passe doit contenir au moins 8 caractères.'], 400);
            }

            // Updates the password with the hashed one
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            $user->setExpiresAt(null);
            $user->setResetKey(null);
            $entityManager->flush();

            return $this->json(
                [
                    'message' => [
                        'type' => 'success',
                        'content' => 'Mot de passe réinitialisé avec succès !'
                    ],
                    'redirect_to' => '/login'
                ],
                201
            );
        }
    }

    /**
     * Retrieves all roles of the currently authenticated user.
     *
     * This endpoint retrieves all roles assigned to the currently authenticated user.
     * It is accessible only when the user is authenticated.
     * It is used by the frontend to determine which home page to display.
     *
     * @Route("/api/get-roles", methods={"GET"})
     */
    #[OA\Response(
        response: 200,
        description: 'Array of roles (contains at least the default role ROLE_USER)',
    )]
    #[OA\Tag(name: 'User roles')]
    #[Security(name: 'Bearer')]
    #[Route('/api/get-roles', name: 'getRoles', methods: ['GET'])]
    public function getRoles(): JsonResponse {
        return $this->json(['roles' => $this->getUser()->getRoles()]);
    }

}

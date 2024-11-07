<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twilio\Exceptions\ConfigurationException;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;

class VisitorRequestController extends AbstractController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    private \Twilio\Rest\Client $twilioClient;

    /**
     * @throws ConfigurationException
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, )
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->twilioClient = new \Twilio\Rest\Client(
            "AC6df8360625dc643d7a1c60c6a6ec5e1e",
            "7346fbd6d6b8c02c40a199d34cb84fd3"
        );
    }

    /**
     * Creates a visitor request.
     *
     * This endpoint is used to create a visitor request. It is accessible only to users who are not already visitors.
     * It requires the following parameters in the request body:
     * - 'hourlyRate': The hourly rate for the visitor.
     * - 'address': The visitor's address.
     * - 'phone': The visitor's phone number.
     *
     * If all required fields are provided, the visitor request is created successfully, and a success message is returned.
     * Otherwise, an error message indicating the missing fields or invalid data format is returned.
     *
     * @Route("/api/visitor-request/create", methods={"POST"})
     */
    #[OA\Parameter(
        name: 'hourlyRate',
        description: 'The hourly rate for the visitor',
        in: 'query',
        schema: new OA\Schema(type: 'number', format: 'float')
    )]
    #[OA\Parameter(
        name: 'address',
        description: "The visitor's address",
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'phone',
        description: "The visitor's phone number",
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Visitor request')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visitor-request/create', name: 'createVisitorRequest', methods: ['POST'])]
    public function createVisitorRequest(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        ValidatorInterface $validator,
    ): JsonResponse {
        // Checks if the user is already a visitor
        if ($this->isGranted('ROLE_VISITOR')) {
            return $this->json(['error' => 'Vous êtes déjà un visiteur.'], 400);
        }

        // Retrieves values from the request
        $hourlyRate = $request->request->get('hourlyRate');
        $address = $request->request->get('address');
        $phone = $request->request->get('phone');

        // Checks if all fields have a value
        if (empty($hourlyRate) || empty($address) || empty($phone)) {
            return $this->json(['error' => 'Tous les champs doivent être remplis.'], 400);
        }

        // Regular expression for the phone number format
        $phoneRegex = '/^\+33[1-9][0-9]{8}$/';

        // Validates the phone number format
        $phoneViolations = $validator->validate($phone, [
            new Assert\Regex([
                'pattern' => $phoneRegex,
                'message' => 'Format du numéro de téléphone incorrect. Il doit satisfaire le format +33XXXXXXXXX.',
            ]),
        ]);

        // Check if there are any validation errors
        if (count($phoneViolations) > 0) {
            return $this->json(['error' => $phoneViolations[0]->getMessage()], 400);
        }

        // Checks if the hourly rate has a valid format
        $rateViolations = $validator->validate($hourlyRate, [
            new Assert\Regex([
                'pattern' => '/^\d+(\.\d{1,2})?$/',
                'message' => 'Le format du taux horaire est invalide. Il doit être un nombre avec au maximum 2 décimales.',
            ]),
            new Assert\Range([
                'max' => 200,
                'maxMessage' => 'Le taux horaire ne peut pas dépasser 200.',
            ]),
        ]);

        // Check if there are any validation errors
        if (count($rateViolations) > 0) {
            return $this->json(['error' => $rateViolations[0]->getMessage()], 400);
        }

        // Retrieves the user entity
        $user = $userRepository->find($this->getUser()->getId());

        // Sets the user's phone, address, and hourly rate
        $user->setPhone($phone);
        $user->setAddress($address);
        $user->setHourlyRate($hourlyRate);

        // Persists the changes to the database
        $entityManager->persist($user);
        $entityManager->flush();

        // Sends the SMS to validate the phone number
        $this->sendSMS();

        return $this->json(
            [
                'message' => [
                    'type' => 'success',
                    'content' => 'Un SMS vous a été envoyé pour valider votre numéro de téléphone et devenir visiteur !'
                ],
            ],
            201
        );
    }

    /**
     * Resends the SMS code for visitor verification.
     *
     * This endpoint is used to resend the SMS code for visitor verification.
     * It does not require any parameters.
     *
     * If the SMS code is successfully resent, a success message is returned.
     *
     * @Route("/api/visitor-request/resend-sms", methods={"POST"})
     */
    #[OA\Tag(name: 'Visitor request')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visitor-request/resend-sms', name: 'resendSMS', methods: ['POST'])]
    public function resendSMS(): JsonResponse
    {
        // Sends the SMS
        $this->sendSMS();

        return $this->json(
            [
                'message' => [
                    'type' => 'success',
                    'content' => 'SMS renvoyé !'
                ],
            ],
            201
        );
    }


    /**
     * Sends an SMS verification code to the user's phone number.
     * Generates a random verification code, sets it for the user, and sends it via Twilio.
     */
    public function sendSMS(): void
    {
        // Generate a random verification code
        $code = rand(1000, 9999);

        // Retrieve the user entity
        $user = $this->userRepository->find($this->getUser()->getId());

        // If user exists, set the verification code and persist changes
        if ($user) {
            $user->setSmsVerificationCode($code);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Send SMS using Twilio
            $this->twilioClient->messages->create(
                $user->getPhone(),
                [
                    'from' => '+12135834642',
                    'body' => 'Pour valider votre numéro de téléphone, veuillez entrer le code suivant : ' . $code,
                ]
            );
        }
    }


    /**
     * Verifies the SMS verification code.
     *
     * This endpoint is used to verify the SMS verification code received by the user.
     * It requires the following parameter in the request body:
     * - 'code': The SMS verification code entered by the user.
     *
     * If the code matches the one associated with the user, the user's roles are updated to include 'ROLE_CLIENT' and 'ROLE_VISITOR',
     * and a success message is returned along with a redirection URL to the profile page.
     * Otherwise, an error message indicating that the code is incorrect is returned.
     *
     * @Route("/api/visitor-request/verify-sms-code", methods={"POST"})
     */
    #[OA\Parameter(
        name: 'code',
        description: 'The SMS verification code',
        in: 'query',
        schema: new OA\Schema(type: 'integer', format: 'int32')
    )]
    #[OA\Tag(name: 'Visitor request')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visitor-request/verify-sms-code', name: 'verifySMSCode', methods: ['POST'])]
    public function verifySMSCode(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        // Retrieves the values from the request
        $user = $userRepository->find($this->getUser()->getId());
        $code = $request->request->get('code');

        // If user exists
        if ($user) {
            // Checks if the provided code matches the stored verification code
            if ($code == $user->getSmsVerificationCode()) {
                // If the codes match, grant the user roles and persist changes
                $user->setRoles(["ROLE_CLIENT", "ROLE_VISITOR"]);
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->json(
                    [
                        'message' => [
                            'type' => 'success',
                            'content' => 'Téléphone validé avec succès ! Vous êtes desormais un visiteur !'
                        ],
                        'redirect_to' => '/profile'
                    ],
                    201
                );
            } else {
                // If the codes don't match, return an error response
                return $this->json(['error' => 'Le code est incorrect.'], 400);
            }
        }

        // If no user is found, return an error response
        return $this->json(['error' => "Aucun utilisateur n'a été trouvé."], 400);
     }
}

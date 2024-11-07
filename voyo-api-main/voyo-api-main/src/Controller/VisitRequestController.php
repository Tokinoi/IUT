<?php

namespace App\Controller;

use App\Entity\VerificationPoint;
use App\Entity\VisitRequest;
use App\Repository\UserRepository;
use App\Repository\VerificationPointRepository;
use App\Repository\VisitRequestRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Meilisearch\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;

class VisitRequestController extends AbstractController
{

    /**
     * Retrieves visit requests.
     *
     * This endpoint retrieves visit requests and can only be accessed by an admin.
     * Visit requests can be filtered by a search term ('query') and/or a specific state ('state').
     *
     * @Route("/api/visit-requests", methods={"GET"})
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the visit requests',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: VisitRequest::class))
        )
    )]
    #[OA\Parameter(
        name: 'query',
        description: 'The search term to filter visit requests',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'state',
        description: 'Filter visit requests by specific state',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Visit requests')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visit-requests', name: 'visitRequests', methods: ['GET'])]
    public function visitRequests(
        Request $request,
    ): JsonResponse
    {
        // Gets the Meilisearch URL and key from parameters
        $meilisearchUrl = $this->getParameter('app.meilisearchUrl');
        $meilisearchKey = $this->getParameter('app.meilisearchKey');

        // Creates a new Meilisearch client instance
        $client = new Client($meilisearchUrl, $meilisearchKey);

        // Gets query and state parameters from the request
        $query = $request->query->get('query') ?? '';
        $state = $request->query->get('state');

        // Sets up query parameters
        $queryParams = [
            'limit' => 20,
            'attributesToHighlight' => ['*'],
            'highlightPreTag' => '<span class="highlight">',
            'highlightPostTag' => '</span>'
        ];

        // If state parameter is provided, add it to the filter
        if ($state) {
            $queryParams['filter'] = 'state = ' . $state;
        }

        // Performs the search on the VisitRequest index in Meilisearch
        $result = $client->index('VisitRequest')->search($query, $queryParams);

        // Returns the search result as JSON response
        return $this->json($result);
    }

    /**
     * Retrieves visit requests associated with the authenticated user.
     *
     * This endpoint is used to list visit requests for the visitor or client.
     * On the homepage of a client or visitor, they can view a list of visit requests they are involved in
     * (either as a client or as a visitor).
     *
     * @Route("/api/my-visit-requests", methods={"GET"})
     */
    #[OA\Response(
        response: 200,
        description: 'Returns visit requests associated with the authenticated user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: VisitRequest::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'Visit requests')]
    #[Security(name: 'Bearer')]
    #[Route('/api/my-visit-requests', name: 'listMyVisitRequests', methods: ['GET'])]
    public function listMyVisitRequests(): JsonResponse
    {
        // Gets the Meilisearch URL and key from parameters
        $meilisearchUrl = $this->getParameter('app.meilisearchUrl');
        $meilisearchKey = $this->getParameter('app.meilisearchKey');

        // Creates a new Meilisearch client instance
        $client = new Client($meilisearchUrl, $meilisearchKey);

        // Initializes the filter based on user's role
        if ($this->isGranted("ROLE_VISITOR")) {
            $filter = "visitor.id = " . $this->getUser()->getId() . " OR client.id = " . $this->getUser()->getId();
        } else {
            $filter = "client.id = " . $this->getUser()->getId();
        }

        // Performs the search on the VisitRequest index in Meilisearch
        $result = $client->index('VisitRequest')->search('', [
            'filter' =>  $filter
        ]);

        // Returns the search result as JSON response
        return $this->json($result);
    }

    /**
     * Creates a visit request.
     *
     * This endpoint is used to create a visit request by a client or a visitor.
     * It either returns an error message or a success one.
     *
     * @Route("/api/visit-request/create", methods={"POST"})
     */
    #[OA\Parameter(
        name: 'propertyAddress',
        description: "The address of the property",
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'propertyCity',
        description: "The city of the property",
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'propertyPostalCode',
        description: "The postal code of the property",
        in: 'query',
        schema: new OA\Schema(type: 'integer', format: 'int32')
    )]
    #[OA\Parameter(
        name: 'price',
        description: "The price of the visit",
        in: 'query',
        schema: new OA\Schema(type: 'number', format: 'float')
    )]
    #[OA\Parameter(
        name: 'scheduledAt',
        description: "The scheduled date of the visit",
        in: 'query',
        schema: new OA\Schema(type: 'string', format: 'date')
    )]
    #[OA\Tag(name: 'Visit requests')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visit-request/create', name: 'createVisitRequest', methods: ['POST'])]
    public function createVisitRequest(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
    ): JsonResponse {

        // Retrieves the data from the request
        $propertyAddress = $request->request->get('propertyAddress');
        $propertyCity = $request->request->get('propertyCity');
        $propertyPostalCode = $request->request->get('propertyPostalCode');
        $price = $request->request->get('price');
        $scheduledAtString = $request->request->get('scheduledAt');

        // Converts scheduledAtString to DateTimeImmutable object if provided
        if ($scheduledAtString) {
            // Convert to date
            $scheduledAt = DateTimeImmutable::createFromFormat('d-m-Y', $scheduledAtString);

            if ($scheduledAt === false) {
                return $this->json(['error' => "La date programmée n'est pas au format valide."], 400);
            }
        }

        // Checks if all required fields have a value
        if (empty($propertyAddress) || empty($propertyCity) || empty($propertyPostalCode) || empty($price)) {
            return $this->json(['error' => "L'adresse du bien et le prix de la visite doivent être renseignés."], 400);
        }

        // Verify the price format
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $price)) {
            return $this->json(['error' => "Le prix n'est pas au format valide"], 400);
        }

        // Gets the client (user) from the UserRepository
        $client = $userRepository->find($this->getUser()->getId());

        // Creates a new VisitRequest entity and populates it with the provided data
        $visitRequest = new VisitRequest();
        $visitRequest->setClient($client);
        $visitRequest->setPropertyAddress($propertyAddress);
        $visitRequest->setPropertyCity($propertyCity);
        $visitRequest->setPropertyPostalCode($propertyPostalCode);
        $visitRequest->setPrice($price);
        if ($scheduledAtString && $scheduledAt) {
            $visitRequest->setScheduledAt($scheduledAt);
        }
        $visitRequest->setStatus(VisitRequest::STATE_PENDING);
        $visitRequest->setCreationDate(new \DateTime());

        // Persists the entity and flushes the changes to the database
        $entityManager->persist($visitRequest);
        $entityManager->flush();

        return $this->json(
            [
                'message' => [
                    'type' => 'success',
                    'content' => 'Demande créée avec succès !'
                ],
                'redirect_to' => '/visit-request',
                'visit-request-id' => $visitRequest->getId()
            ],
            201
        );
    }

    /**
     * View a visit request.
     *
     * This endpoint retrieves the details of a visit request identified by its unique identifier.
     * It requires the following parameter in the request:
     * - 'id': The unique identifier of the visit request to view.
     *
     * If the visit request exists, its details along with verification points are returned. Otherwise, an error message
     * indicating that the request does not exist is returned.
     *
     * @Route("/api/visit-request/view", methods={"GET"})
     */
    #[OA\Parameter(
        name: 'id',
        description: 'The unique identifier of the visit request',
        in: 'query',
        schema: new OA\Schema(type: 'integer', format: 'int64')
    )]
    #[OA\Tag(name: 'Visit requests')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visit-request/view', name: 'viewVisitRequest', methods: ['GET'])]
    public function viewVisitRequest(
        Request $request,
        VisitRequestRepository $visitRequestRepository,
        VerificationPointRepository $verificationPointRepository,
    ): JsonResponse
    {
        // Finds the visit request entity by ID
        $visitRequest = $visitRequestRepository->find($request->query->get('id'));

        if ($visitRequest) {
            // Prepares the data about the visit request
            $visitRequestData = [
                'isTheConnectedUserTheClient' => ($this->getUser() === $visitRequest->getClient()),
                'isTheConnectedUserTheVisitor' => ($this->getUser() === $visitRequest->getVisitor()),
                'client' => [
                    'displayName' => $visitRequest->getClient()->getDisplayName(),
                    'email' => $visitRequest->getClient()->getEmail(),
                    'city' => $visitRequest->getClient()->getCity(),
                    'address' => $visitRequest->getClient()->getAddress(),
                ],
                'propertyAddress' => $visitRequest->getPropertyAddress(),
                'propertyCity' => $visitRequest->getPropertyCity(),
                'propertyPostalCode' => $visitRequest->getPropertyPostalCode(),
                'price' => $visitRequest->getPrice(),
                'status' => $visitRequest->getStatus(),
                'creationDate' => $visitRequest->getCreationDate()->format('d/m/Y'),
                'scheduledAt' => $visitRequest->getScheduledAt()?->format('d/m/Y'),
            ];

            // Includes the visitor information if exists
            if ($visitRequest->getVisitor()) {
                $visitRequestData['visitor'] = [
                    'displayName' => $visitRequest->getVisitor()->getDisplayName(),
                    'email' => $visitRequest->getVisitor()->getEmail(),
                    'city' => $visitRequest->getVisitor()->getCity(),
                    'address' => $visitRequest->getVisitor()->getAddress(),
                    'phone' => $visitRequest->getVisitor()->getPhone(),
                ];
            }

            // Gets the verification points of the visit request
            $verificationPoints = $verificationPointRepository->findBy(['visitRequest' => $visitRequest]);
            $verificationPointsArray = [];
            foreach ($verificationPoints as $aVerificationPoint) {
                $verificationPointsArray[] = [
                    'id' => $aVerificationPoint->getId(),
                    'title' => $aVerificationPoint->getTitle(),
                    'image' => $aVerificationPoint->getImage(),
                    'isValidated' => $aVerificationPoint->isIsValidated(),
                ];
            }

            $visitRequestData['verificationPoints'] = $verificationPointsArray;

            // Returns the visit request data as JSON response
            return $this->json($visitRequestData);
        } else {
            // Returns an error if visit request not found
            return $this->json(['error' => 'Demande inexistante.'], 400);
        }
    }

    /**
     * Get the image of a verification point.
     *
     * This endpoint retrieves the image associated with a verification point identified by its unique identifier.
     * It requires the following parameter in the request:
     * - 'id': The unique identifier of the verification point.
     *
     * If the verification point exists and has an associated image, the image file is returned. Otherwise, an error
     * message indicating that the verification point or its image does not exist is returned.
     *
     * @Route("/api/verification-point/image", methods={"GET"})
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the image of the verification point',
        content: new OA\MediaType(
            mediaType: 'image/*',
        )
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'The unique identifier of the verification point',
        in: 'query',
        schema: new OA\Schema(type: 'integer', format: 'int64')
    )]
    #[OA\Tag(name: 'Visit requests')]
    #[Security(name: 'Bearer')]
    #[Route('/api/verification-point/image', name: 'verificationPointImage', methods: ['GET'])]
    public function getVerificationPointImage(
        Request $request,
        VerificationPointRepository $verificationPointRepository,
        KernelInterface $kernel
    ): BinaryFileResponse | JsonResponse
    {
        // Finds the verification point entity by ID
        $verificationPoint = $verificationPointRepository->find($request->query->get('id'));

        if (!$verificationPoint) {
            // If verification point not found, return error JSON response
            return $this->json(['error' => "Le point de vérification est inconnu."], 400);
        }

        // Checks if the verification point has an image
        if ($verificationPoint->getImage()) {
            // Gets the absolute path of the image file
            $photoPath = $kernel->getProjectDir() . '/Images/VisitRequests/'. $verificationPoint->getImage();

            // Checks if the image file exists
            if (file_exists($photoPath)) {
                // Returns the image file as a binary response
                return new BinaryFileResponse($photoPath);
            }
        }

        // If the image file does not exist, return error JSON response
        return $this->json(['error' => 'Image inexistante.'], 401);
    }


    /**
     * Edit a visit request.
     *
     * This endpoint allows editing a visit request identified by its unique identifier. It accepts various parameters
     * in the request body to modify different aspects of the visit request, such as property address, city, postal code,
     * price, scheduled date, and verification points.
     *
     * It responds with a success message if the visit request is successfully edited, along with the updated visit
     * request identifier for redirection. In case of any errors, it returns an appropriate error message.
     *
     * @Route("/api/visit-request/edit", methods={"POST"})
     */
    #[OA\Parameter(
        name: 'id',
        description: 'The unique identifier of the visit request to edit',
        in: 'query',
        schema: new OA\Schema(type: 'integer', format: 'int64')
    )]
    #[OA\Parameter(
        name: 'propertyAddress',
        description: 'New address of the property (optional)',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'propertyCity',
        description: 'New city of the property (optional)',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'propertyPostalCode',
        description: 'New postal code of the property (optional)',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'price',
        description: 'New price of the visit (optional)',
        in: 'query',
        schema: new OA\Schema(type: 'number')
    )]
    #[OA\Parameter(
        name: 'scheduledAt',
        description: 'New scheduled date of the visit (optional, format: dd-mm-yyyy)',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'verificationPoints',
        description: 'List of descriptions of new verification points (optional)',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Visit requests')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visit-request/edit', name: 'editVisitRequest', methods: ['POST'])]
    public function editVisitRequest(
        Request $request,
        EntityManagerInterface $entityManager,
        VisitRequestRepository $visitRequestRepository
    ): JsonResponse {
        // Retrieves the visit request entity by ID
        $visitRequest = $visitRequestRepository->find($request->request->get('id'));

        // Checks if the visit request exists
        if (!$visitRequest) {
            return $this->json(['error' => "La demande de visite est inconnue."], 400);
        }

        // Retrieves the data from the request
        $propertyAddress = $request->request->get('propertyAddress');
        $propertyCity = $request->request->get('propertyCity');
        $propertyPostalCode = $request->request->get('propertyPostalCode');
        $price = $request->request->get('price');
        $scheduledAtString = $request->request->get('scheduledAt');

        // Updates the scheduled date if provided
        if ($scheduledAtString) {
            $scheduledAt = DateTimeImmutable::createFromFormat('d-m-Y', $scheduledAtString);
            if ($scheduledAt === false) {
                return $this->json(['error' => "La date programmée n'est pas au format valide."], 400);
            }
            $visitRequest->setScheduledAt($scheduledAt);
        }


        // Updates the other fields if provided
        if ($propertyAddress) {
            $visitRequest->setPropertyAddress($propertyAddress);
        }
        if ($propertyCity) {
            $visitRequest->setPropertyCity($propertyCity);
        }
        if ($propertyPostalCode) {
            $visitRequest->setPropertyPostalCode($propertyPostalCode);
        }
        if ($price) {
            // Verify the price format
            if (!preg_match('/^\d+(\.\d{1,2})?$/', $price)) {
                return $this->json(['error' => "Le prix n'est pas au format valide"], 400);
            }
            $visitRequest->setPrice($price);
        }
        $entityManager->persist($visitRequest);
        $entityManager->flush();

        // Updates the verification points if provided
        $verificationPointsString = $request->request->get('verificationPoints');
        $verificationPoints = json_decode($verificationPointsString, true);

        // Adds the new verification points
        if (!empty($verificationPoints)) {
            foreach ($verificationPoints as $verificationPointTitle) {
                $verificationPoint = new VerificationPoint();
                $verificationPoint->setVisitRequest($visitRequest);
                $verificationPoint->setTitle($verificationPointTitle);
                $verificationPoint->setIsValidated(false);
                $entityManager->persist($verificationPoint);
            }
        }

        $entityManager->flush();

        // Returns a success JSON response
        return $this->json(
            [
                'message' => [
                    'type' => 'success',
                    'content' => 'Demande modifiée avec succès !'
                ],
                'redirect_to' => '/visit-request',
                'visit-request-id' => $visitRequest->getId()
            ],
            201
        );
    }

    /**
     * Delete a verification point.
     *
     * This endpoint allows deleting a verification point identified by its unique identifier.
     * It should be accessible only by the client of the visit request.
     *
     * @Route("/api/verification-point/delete", methods={"POST"})
     */
    #[OA\Parameter(
        name: 'id',
        description: 'The unique identifier of the verification point to delete',
        in: 'query',
        schema: new OA\Schema(type: 'integer', format: 'int64')
    )]
    #[OA\Tag(name: 'Visit requests')]
    #[Security(name: 'Bearer')]
    #[Route('/api/verification-point/delete', name: 'deleteVerificationPoint', methods: ['POST'])]
    public function deleteVerificationPoint(
        Request $request,
        EntityManagerInterface $entityManager,
        VerificationPointRepository $verificationPointRepository,
        UserRepository $userRepository
    ): JsonResponse {
        $verificationPoint = $verificationPointRepository->find($request->request->get('id'));

        // Checks if the verification point exists
        if (!$verificationPoint) {
            return $this->json(['error' => "Le point de vérification est inconnu."], 400);
        }

        // Should be accessible only by the client of the visit request
        $user = $userRepository->find($this->getUser());
        if ($user !== $verificationPoint->getVisitRequest()->getClient()) {
            return $this->json(['error' => "Action non autorisée."], 400);
        }

        // Removes the verification point
        $entityManager->remove($verificationPoint);
        $entityManager->flush();

        return $this->json(
            [
                'message' => [
                    'type' => 'success',
                    'content' => 'Point de vérification supprimé !'
                ],
                'redirect_to' => '/visit-request',
                'visit-request-id' => $verificationPoint->getVisitRequest()->getId()
            ],
            201
        );
    }


    /**
     * Validate or invalidate a verification point and optionally add a comment.
     *
     * This endpoint allows validating or invalidating a verification point identified by its unique identifier.
     * It also allows adding a comment related to the verification point.
     *
     * @Route("/api/verification-point/validate", methods={"POST"})
     */
    #[OA\Parameter(
        name: 'id',
        description: 'The unique identifier of the verification point to validate or invalidate',
        in: 'query',
        schema: new OA\Schema(type: 'integer', format: 'int64')
    )]
    #[OA\Parameter(
        name: 'comment',
        description: 'An optional comment related to the verification point',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Visit requests')]
    #[Security(name: 'Bearer')]
    #[Route('/api/verification-point/validate', name: 'validateVerificationPoint', methods: ['POST'])]
    public function validateVerificationPoint(
        Request $request,
        EntityManagerInterface $entityManager,
        VerificationPointRepository $verificationPointRepository
    ): JsonResponse {
        // Retrieves the verification point entity by ID
        $verificationPoint = $verificationPointRepository->find($request->request->get('id'));

        // Checks if the verification point exists
        if (!$verificationPoint) {
            return $this->json(['error' => "Le point de vérification est inconnu."], 400);
        }

        // Retrieves comment from the request
        $comment = $request->request->get('comment');

        // Updates the verification point's comment if provided
        if ($comment) {
            $verificationPoint->setComment($comment);
        }

        // Toggles the validation status of the verification point
        $verificationPoint->setIsValidated(!$verificationPoint->isIsValidated());

        // Persists changes to the database
        $entityManager->persist($verificationPoint);
        $entityManager->flush();

        // Returns success JSON response
        return $this->json(
            [
                'message' => [
                    'type' => 'success',
                    'content' => 'Point de vérification mis à jour !'
                ],
                'redirect_to' => '/visit-request',
                'verification-point-id' => $verificationPoint->getId(),
                'visit-request-id' => $verificationPoint->getVisitRequest()->getId()
            ],
            201
        );
    }

    /**
     * Add an image for a verification point of a visit request.
     *
     * Each verification point can have an optional image. The visitor can upload an image for each verification point
     * during the report. When the visitor clicks on the "add image" button for a verification point, this endpoint
     * is called with the following parameters:
     * - id: The ID of the verification point
     * - image: The image file to upload
     *
     * @Route("/api/verification-point/add-image", methods={"POST"})
     */
    #[OA\Parameter(
        name: 'id',
        description: 'The unique identifier of the verification point',
        in: 'query',
        schema: new OA\Schema(type: 'integer', format: 'int64')
    )]
    #[OA\Tag(name: 'Visit requests')]
    #[Security(name: 'Bearer')]
    #[Route('/api/verification-point/add-image', name: 'addImageVerificationPoint', methods: ['POST'])]
    public function addImageVerificationPoint(
        Request $request,
        VerificationPointRepository $verificationPointRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        KernelInterface $kernel
    ): JsonResponse
    {
        // Retrieves the verification point
        $verificationPoint = $verificationPointRepository->find($request->request->get('id'));

        // Checks the existence of the verification point
        if (!$verificationPoint) {
            return $this->json(['error' => "Le point de vérification est inconnu."], 400);
        }

        // Should be accessible only by the visitor of the visit request
        $user = $userRepository->find($this->getUser());
        if (!$this->isGranted('ROLE_VISITOR') || $user !== $verificationPoint->getVisitRequest()->getVisitor()) {
            return $this->json(['error' => "Action non autorisée."], 400);
        }

        // Update the verification point image
        $uploadedFile = $request->files->get('image');
        if ($uploadedFile instanceof UploadedFile) {
            // Creates the filepath of the image
            $filePath = $kernel->getProjectDir() . '/Images/VisitRequests/';

            // Creates the directory in case it doesn't exist
            if (!file_exists($filePath)) {
                mkdir($filePath, 0755, true);
            }

            // Creates the filename of the image
            $filename = 'verificationPoint' . $verificationPoint->getId() . '.' . $uploadedFile->guessExtension();
            try {
                // Stores the image in the server and links it to the verification point
                $uploadedFile->move($filePath, $filename);
                $verificationPoint->setImage($filename);
                $entityManager->persist($verificationPoint);
                $entityManager->flush();
            } catch (FileException $e) {
                throw new FileException('Erreur lors du téléchargement du fichier');
            }
        } else {
            return $this->json(['error' => "Aucune image n'a été trouvée."], 400);
        }

        return $this->json(
            [
                'message' => [
                    'type' => 'success',
                    'content' => 'Image ajoutée au point de vérification !'
                ],
                'redirect_to' => '/visit-request',
                'verification-point-id' => $verificationPoint->getId(),
                'visit-request-id' => $verificationPoint->getVisitRequest()->getId()
            ],
            201
        );
    }

    /**
     * Edit the report of a visit request.
     *
     * During the report, the visitor can edit the general report of the visit request. They can add any comments
     * or details that are not necessarily related to existing verification points. To achieve this, the visitor
     * should call the endpoint: /api/visit-request/edit-report (POST) with the following parameters:
     * - id: The ID of the visit request
     * - report: The report text
     *
     * @Route("/api/visit-request/edit-report", methods={"POST"})
     */
    #[OA\Parameter(
        name: 'id',
        description: 'The unique identifier of the visit request',
        in: 'query',
        schema: new OA\Schema(type: 'integer', format: 'int64')
    )]
    #[OA\Tag(name: 'Visit requests')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visit-request/edit-report', name: 'editReportVisitRequest', methods: ['POST'])]
    public function editReportVisitRequest(
        Request $request,
        EntityManagerInterface $entityManager,
        VisitRequestRepository $visitRequestRepository,
        UserRepository $userRepository,
    ): JsonResponse {
        // Retrieves the visit request entity by ID
        $visitRequest = $visitRequestRepository->find($request->request->get('id'));

        // Checks if the visit request exists
        if (!$visitRequest) {
            return $this->json(['error' => "La demande de visite est inconnue."], 400);
        }

        // Should be accessible only by the visitor of the visit request
        $user = $userRepository->find($this->getUser());
        if (!$this->isGranted('ROLE_VISITOR') || $user !== $visitRequest->getVisitor()) {
            return $this->json(['error' => "Action non autorisée."], 400);
        }

        // Retrieves the report from the request
        $report = $request->request->get('report');

        // Updates the visit request's report if provided
        if ($report) {
            $visitRequest->setReport($report);
            $entityManager->persist($visitRequest);
            $entityManager->flush();
        } else {
            return $this->json(['error' => "Aucun compte rendu n'a été trouvé."], 400);
        }

        // Returns the success JSON response
        return $this->json(
            [
                'message' => [
                    'type' => 'success',
                    'content' => 'Demande modifiée avec succès !'
                ],
                'redirect_to' => '/visit-request',
                'visit-request-id' => $visitRequest->getId()
            ],
            201
        );
    }

    /**
     * Pay for a visit request.
     *
     * To transition a visit request from PENDING state (waiting for a visitor and payment) to IN PROGRESS state (confirmed),
     * the visit request must already have a visitor and be paid for by the client. When the visit request has a visitor
     * and its status is still pending, a pay request button should be displayed on the visit request page for the client.
     * When the client clicks on this button, call: /api/visit-request/pay (POST) with the parameter: visit-request-id.
     *
     * @Route("/api/visit-request/pay", methods={"POST"})
     */
    #[OA\Parameter(
        name: 'id',
        description: 'The unique identifier of the visit request',
        in: 'query',
        schema: new OA\Schema(type: 'integer', format: 'int64')
    )]
    #[OA\Tag(name: 'Visit requests')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visit-request/pay', name: 'payVisitRequest', methods: ['POST'])]
    public function payVisitRequest(
        Request $request,
        EntityManagerInterface $entityManager,
        VisitRequestRepository $visitRequestRepository,
        UserRepository $userRepository,
    ): JsonResponse {
        // Retrieves the visit request entity by ID
        $visitRequest = $visitRequestRepository->find($request->request->get('id'));

        // Checks if the visit request exists
        if (!$visitRequest) {
            return $this->json(['error' => "La demande de visite est inconnue."], 400);
        }

        // Should be accessible only by the visitor of the visit request
        $user = $userRepository->find($this->getUser());
        if (!$this->isGranted('ROLE_VISITOR') || $user !== $visitRequest->getVisitor()) {
            return $this->json(['error' => "Action non autorisée."], 400);
        }

        // Checks if the visit request is in pending state
        if ($visitRequest->getStatus() == VisitRequest::STATE_PENDING) {
            // Updates the visit request status to in-progress
            $visitRequest->setStatus(VisitRequest::STATE_IN_PROGRESS);
            $entityManager->persist($visitRequest);
            $entityManager->flush();
        } else {
            return $this->json(['error' => "La demande de visite a déjà été réglée."], 400);
        }

        // Returns success JSON response
        return $this->json(
            [
                'message' => [
                    'type' => 'success',
                    'content' => 'Demande de visite payée avec succès !'
                ],
                'redirect_to' => '/visit-request',
                'visit-request-id' => $visitRequest->getId()
            ],
            201
        );
    }

    /**
     * Cancel a visit request.
     *
     * Both the visitor and the client of the visit request can cancel it at any stage of its lifecycle.
     * To cancel a visit request, call the endpoint: /api/visit-request/annulate (POST) with the visit request ID.
     *
     * @Route("/api/visit-request/annulate", methods={"POST"})
     */
    #[OA\Parameter(
        name: 'id',
        description: 'The unique identifier of the visit request',
        in: 'query',
        schema: new OA\Schema(type: 'integer', format: 'int64')
    )]
    #[OA\Tag(name: 'Visit requests')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visit-request/annulate', name: 'annulateVisitRequest', methods: ['POST'])]
    public function annulateVisitRequest(
        Request $request,
        EntityManagerInterface $entityManager,
        VisitRequestRepository $visitRequestRepository,
        UserRepository $userRepository,
    ): JsonResponse {
        // Retrieves the visit request entity by ID
        $visitRequest = $visitRequestRepository->find($request->request->get('id'));

        // Checks if the visit request exists
        if (!$visitRequest) {
            return $this->json(['error' => "La demande de visite est inconnue."], 400);
        }

        // Should be accessible only by the visitor or the client of the visit request
        $user = $userRepository->find($this->getUser());

        // Checks if the current user is either the visitor or the client of the visit request
        if ($user !== $visitRequest->getVisitor() && $user !== $visitRequest->getClient()) {
            return $this->json(['error' => "Action non autorisée."], 400);
        }

        // Checks if the visit request is not already canceled
        if ($visitRequest->getStatus() !== VisitRequest::STATE_CANCELED) {
            // Updates the visit request status to canceled
            $visitRequest->setStatus(VisitRequest::STATE_CANCELED);
            $entityManager->persist($visitRequest);
            $entityManager->flush();
        } else {
            return $this->json(['error' => "La demande de visite est déjà annulée."], 400);
        }

        // Returns success JSON response
        return $this->json(
            [
                'message' => [
                    'type' => 'success',
                    'content' => 'Demande de visite annulée avec succès !'
                ],
                'redirect_to' => '/visit-request',
                'visit-request-id' => $visitRequest->getId()
            ],
            201
        );
    }

    /**
     * Complete a visit request.
     *
     * When the visitor has finished providing the visit report and is satisfied with the result, they should close the
     * request to send the information to the client. To do this, call the endpoint: /api/visit-request/complete with the
     * visit request ID.
     *
     * @Route("/api/visit-request/complete", methods={"POST"})
     */
    #[OA\Parameter(
        name: 'id',
        description: 'The unique identifier of the visit request',
        in: 'query',
        schema: new OA\Schema(type: 'integer', format: 'int64')
    )]
    #[OA\Tag(name: 'Visit requests')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visit-request/complete', name: 'completeVisitRequest', methods: ['POST'])]
    public function completeVisitRequest(
        Request $request,
        EntityManagerInterface $entityManager,
        VisitRequestRepository $visitRequestRepository,
        UserRepository $userRepository,
    ): JsonResponse {
        // Retrieves the visit request entity by ID
        $visitRequest = $visitRequestRepository->find($request->request->get('id'));

        // Checks if the visit request exists
        if (!$visitRequest) {
            return $this->json(['error' => "La demande de visite est inconnue."], 400);
        }

        // Should be accessible only by the visitor of the visit request
        $user = $userRepository->find($this->getUser());

        // Checks if the current user is the visitor of the visit request
        if (!$this->isGranted('ROLE_VISITOR') || $user !== $visitRequest->getVisitor()) {
            return $this->json(['error' => "Action non autorisée."], 400);
        }

        // Checks if the visit request is not already completed
        if ($visitRequest->getStatus() !== VisitRequest::STATE_COMPLETED) {
            // Checks if the report is completed
            if ($visitRequest->getReport()) {
                // Updates the visit request status to completed
                $visitRequest->setStatus(VisitRequest::STATE_COMPLETED);
                $entityManager->persist($visitRequest);
                $entityManager->flush();
            } else {
                return $this->json(['error' => "Veuillez compléter le compte rendu avant de clôturer la demande de visite."], 400);
            }

        } else {
            return $this->json(['error' => "La demande de visite est déjà terminée."], 400);
        }

        // Returns success JSON response
        return $this->json(
            [
                'message' => [
                    'type' => 'success',
                    'content' => 'Demande de visite terminée avec succès !'
                ],
                'redirect_to' => '/visit-request',
                'visit-request-id' => $visitRequest->getId()
            ],
            201
        );
    }

    /**
     * Rate a visit request.
     *
     * To rate a user (visitor/client of their visit request), call the endpoint '/api/visit-request/rate' (POST request).
     * Provide the following data:
     *   - 'id': (ID OF THE VISIT REQUEST)
     *   - 'rating': (AN INTEGER BETWEEN 1 AND 5)
     *
     * @Route("/api/visit-request/rate", methods={"POST"})
     */
    #[OA\Tag(name: 'Visit requests')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visit-request/rate', name: 'rateVisitRequest', methods: ['POST'])]
    public function rateVisitRequest(
        Request $request,
        EntityManagerInterface $entityManager,
        VisitRequestRepository $visitRequestRepository,
        UserRepository $userRepository,
    ): JsonResponse {
        // Retrieves the visit request entity by ID
        $visitRequest = $visitRequestRepository->find($request->request->get('id'));

        // Checks if the visit request exists
        if (!$visitRequest) {
            return $this->json(['error' => "La demande de visite est inconnue."], 400);
        }

        // Retrieves the rating from the request and convert it to integer
        $stringRating = $request->request->get('rating');
        $rating = intval($stringRating);

        // Checks if the rating is between 1 and 5
        if ($rating < 1 || $rating > 5) {
            return $this->json(['error' => 'La note doit être comprise entre 1 et 5.'], 400);
        }

        // Retrieves the current user
        $user = $userRepository->find($this->getUser());

        // Checks if the current user is either the client or the visitor of the visit request
        if ($user !== $visitRequest->getClient() && $user !== $visitRequest->getVisitor()) {
            return $this->json(['error' => "Action non autorisée."], 400);
        }

        // Checks if the visit request is completed
        if ($visitRequest->getStatus() !== VisitRequest::STATE_COMPLETED) {
            return $this->json(['error' => "La demande doit être terminée avant de noter."], 400);
        }

        // Checks if the user has already rated
        if (($user === $visitRequest->getClient() && $visitRequest->getClientRating()) ||
            ($user === $visitRequest->getVisitor() && $visitRequest->getVisitorRating())) {
            return $this->json(['error' => "Vous avez déjà noté l'autre partie."], 400);
        }

        // Sets the rating based on the user role
        if ($user === $visitRequest->getClient()) {
            $visitRequest->setClientRating($rating);
        } elseif ($user === $visitRequest->getVisitor()) {
            $visitRequest->setVisitorRating($rating);
        }

        // Persists changes to the database
        $entityManager->persist($visitRequest);
        $entityManager->flush();

        // Returns success JSON response
        return $this->json(
            [
                'message' => [
                    'type' => 'success',
                    'content' => 'Utilisateur noté avec succès !'
                ],
                'redirect_to' => '/visit-request',
                'visit-request-id' => $visitRequest->getId()
            ],
            201
        );
    }


}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\VisitRequestRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;

class DefaultController extends AbstractController
{
    /**
     * Fetches profile data of the authenticated user or another user by ID.
     *
     * This endpoint retrieves profile information. When accessed without providing an ID,
     * it returns the profile of the authenticated user. To view another user's profile,
     * provide the user's ID as a query parameter: 'id': 1.
     *
     * If the user is not authenticated or the provided ID does not correspond to any user,
     * an error message will be returned:
     * { "error" : "Utilisateur introuvable." }
     *
     * Profile data returned includes:
     * - 'displayName'
     * - 'email'
     * - 'city'
     * - 'radius': An integer representing the search radius
     * - 'biography'
     * - 'rating': The user's rating, if available. If the user has not been rated yet, it returns "Sans note".
     * - 'availabilityDays': An array containing available days (e.g., ["Fri", "Mon"])
     * - 'availabilityStartingHour': Starting hour of availability (e.g., 11:00:00)
     * - 'availabilityEndingHour': Ending hour of availability (e.g., 17:00:00)
     *
     * Notes:
     * - 'availabilityDays' attribute is an array containing acceptable days: ["Fri", "Mon", "Tue", "Wed", "Thu", "Sat", "Sun"]
     * - 'radius' attribute is an integer.
     * - 'availabilityStartingHour' and 'availabilityEndingHour' attributes are of type time (e.g., 17:00:00).
     *
     * @Route("/api/profile", methods={"GET"})
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the profile data of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['full']))
        )
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'The ID of the user whose profile is to be retrieved',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'profile')]
    #[Security(name: 'Bearer')]
    #[Route('/api/profile', name: 'userProfile', methods: ['GET'])]
    public function userProfile(
        Request $request,
        UserRepository $userRepository,
        VisitRequestRepository $visitRequestRepository
    ): JsonResponse
    {
        // Obtains the user ID from the request query parameters, or uses the authenticated user's ID if not provided
        $id = $request->query->get('id');

        if ($id) {
            $user = $userRepository->find($id);
        } else {
            $user = $userRepository->find($this->getUser()->getId());
        }

        // Returns an error if the user is not found
        if (!$user) {
            return $this->json(['error' => 'Utilisateur introuvable.'], 401);
        }

        // Retrieves visit requests where the user is the client and has given a rating,
        // and visit requests where the user is the visitor and has given a rating
        $ratedVisitRequestsAsClient = $visitRequestRepository->findBy(['client' => $user, 'clientRating' => !null]);
        $ratedVisitRequestsAsVisitor = $visitRequestRepository->findBy(['visitor' => $user, 'visitorRating' => !null]);

        // Calculates the rating of the user based on the visit requests ratings
        $rating = $this->calculateRating($ratedVisitRequestsAsClient, $ratedVisitRequestsAsVisitor);

        // Constructs an array containing user profile data
        $profileData = [
            'id' => $user->getId(),
            'displayName' => $user->getDisplayName(),
            'email' => $user->getEmail(),
            'city' => $user->getCity(),
            'radius' => $user->getRadius(),
            'biography' => $user->getBiography(),
            'rating' => $rating,
            'availabilityDays' => $user->getAvailabilityDays(),
            'availabilityStartingHour' => $user->getAvailabilityStartingHour()?->format('H:i'),
            'availabilityEndingHour' => $user->getAvailabilityEndingHour()?->format('H:i'),
        ];

        return $this->json($profileData);
    }

    /**
     * Calculate the overall rating based on the ratings given by the client and the visitor.
     *
     * This function calculates the overall rating based on the ratings given by the client and the visitor
     * for the visit requests.
     *
     * @param array $ratedVisitRequestsAsClient An array of visit requests rated by the client.
     * @param array $ratedVisitRequestsAsVisitor An array of visit requests rated by the visitor.
     * @return int|float|string The overall rating calculated as the average of ratings given by both the client and the visitor,
     *                          or "Sans note" if there are no ratings.
     */
    public function calculateRating(
        array $ratedVisitRequestsAsClient,
        array $ratedVisitRequestsAsVisitor
    ): int | float | string {
        $ratingsAsClient = 0; // Total ratings given by the client
        $ratingsAsVisitor = 0; // Total ratings given by the visitor
        $totalRatings = 0; // Total number of ratings

        // Calculate the total ratings given by the client and count the number of ratings
        foreach ($ratedVisitRequestsAsClient as $visitRequestAsClient) {
            $ratingsAsClient += $visitRequestAsClient->getClientRating();
            $totalRatings++;
        }

        // Calculate the total ratings given by the visitor and count the number of ratings
        foreach ($ratedVisitRequestsAsVisitor as $visitRequestAsVisitor) {
            $ratingsAsVisitor += $visitRequestAsVisitor->getVisitorRating();
            $totalRatings++;
        }

        // Calculate the overall rating as the average of ratings given by both the client and the visitor
        return ($totalRatings > 0) ? ($ratingsAsClient + $ratingsAsVisitor) / $totalRatings : "Sans note";
    }


    /**
     * Fetches the profile image of the authenticated user or another user by ID.
     *
     * This endpoint retrieves the profile image. When accessed without providing an ID,
     * it returns the profile image of the authenticated user. To view another user's profile image,
     * provide the user's ID as a query parameter: 'id': 1.
     *
     * If the user is not authenticated or the provided ID does not correspond to any user,
     * an error message will be returned:
     * { "error" : "Utilisateur introuvable." }
     *
     * If the user's profile image exists, it will be returned. Otherwise, an error message will be returned.
     *
     * @Route("/api/profile/image", methods={"GET"})
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the profile image of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['full']))
        )
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'The ID of the user whose profile image is to be retrieved',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'profile')]
    #[Security(name: 'Bearer')]
    #[Route('/api/profile/image', name: 'userProfileImage', methods: ['GET'])]
    public function getUserProfileImage(Request $request, UserRepository $userRepository, KernelInterface $kernel): BinaryFileResponse | JsonResponse
    {
        // Obtains the user ID from the request query parameters, or uses the authenticated user's ID if not provided
        $id = $request->query->get('id');

        if ($id) {
            $user = $userRepository->find($id);
        } else {
            $user = $userRepository->find($this->getUser()->getId());
        }

        // Returns an error if the user is not found
        if (!$user) {
            return $this->json(['error' => 'Utilisateur introuvable.'], 401);
        }

        // Checks if the user has an image set
        if ($user->getImageName()) {
            // Constructs the path to the user's profile image
            $photoPath = $kernel->getProjectDir() . '/Images/ProfileImages/'. $user->getImageName();

            // Checks if the image file exists
            if (file_exists($photoPath)) {
                // Returns the profile image as a binary file response
                return new BinaryFileResponse($photoPath);
            }
        }

        return $this->json(['error' => 'Image inexistante.'], 401);
    }

    /**
     * Edits the profile of the authenticated user.
     *
     * This endpoint allows users to edit their profile, including functionalities such as:
     * - Choosing/editing their radius
     * - Adding/editing their availability
     * - Uploading/editing their profile picture
     *
     * To edit the profile, make a POST request to '/api/profile/edit' while authenticated. Provide the following data as needed:
     *
     * {
     *     'email': '',
     *     'city': '',
     *     'radius': 15,
     *     'biography': '',
     *     'availabilityDays': ["Fri", "Mon"],
     *     'availabilityStartingHour': '11:00:00',
     *     'availabilityEndingHour': '17:00:00',
     *     'photo': FILE (image file in the API domain)
     * }
     *
     * Notes:
     * - The 'availabilityDays' attribute must be an array containing acceptable days: ["Fri", "Mon", "Tue", "Wed", "Thu", "Sat", "Sun"].
     * - The 'radius' attribute must be an integer.
     * - The 'availabilityStartingHour' and 'availabilityEndingHour' attributes are of type time (e.g., 17:00:00).
     * - 'photo' is the user's profile image. If provided, it will be updated accordingly.
     *
     * Upon successful profile update, a success message will be returned along with a redirect URL to the user's profile.
     * If there are validation errors or issues with the provided data, an appropriate error message will be returned.
     *
     * @Route("/api/profile/edit", methods={"POST"})
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
        description: 'User email',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'city',
        description: 'User city',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'radius',
        description: 'User search radius',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'biography',
        description: 'User biography',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'availabilityDays',
        description: 'User availability days',
        in: 'query',
        schema: new OA\Schema(
            type: 'string',
        )
    )]
    #[OA\Parameter(
        name: 'availabilityStartingHour',
        description: 'User availability starting hour',
        in: 'query',
        schema: new OA\Schema(type: 'string', format: 'time')
    )]
    #[OA\Parameter(
        name: 'availabilityEndingHour',
        description: 'User availability ending hour',
        in: 'query',
        schema: new OA\Schema(type: 'string', format: 'time')
    )]
    #[OA\Parameter(
        name: 'photo',
        description: 'User profile picture',
        in: 'query',
        schema: new OA\Schema(type: 'string', format: 'binary')
    )]
    #[OA\Tag(name: 'profile')]
    #[Security(name: 'Bearer')]
    #[Route('/api/profile/edit', name: 'editUserProfile', methods: ['POST'])]
    public function editUserProfile(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        KernelInterface $kernel
    ): JsonResponse
    {
        // Gets the user
        $user = $userRepository->find($this->getUser()->getId());

        // Returns an error if the user is not found
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non connecté.'], 401);
        }

        // Update the profile picture
        $uploadedFile = $request->files->get('photo');
        if ($uploadedFile instanceof UploadedFile) {
            $filePath = $kernel->getProjectDir() . '/Images/ProfileImages/';

            // Creates the directory in case it doesn't exist
            if (!file_exists($filePath)) {
                mkdir($filePath, 0755, true);
            }

            $filename = 'userPhoto' . $user->getId() . '.' . $uploadedFile->guessExtension();
            try {
                $uploadedFile->move($filePath, $filename);
                $user->setImageName($filename);
            } catch (FileException $e) {
                throw new FileException('Erreur lors du téléchargement du fichier');
            }
        }

        // Updates user profile information if provided
        if ($request->request->has('email')) {
            $user->setCity($request->request->get('email'));
        }

        if ($request->request->has('city')) {
            $user->setCity($request->request->get('city'));
        }

        if ($request->request->has('radius')) {
            $user->setRadius($request->request->get('radius'));
        }

        if ($request->request->has('biography')) {
            $user->setBiography($request->request->get('biography'));
        }

        // Validates and sets the availability days
        if ($request->request->has('availabilityDays')) {
            // Format validation
            $validDays = $this->validateDaysArray($request->request->get('availabilityDays'));
            if ($validDays) {
                $user->setAvailabilityDays($validDays);
            } else {
                return $this->json(['error' => 'Les données contenant les jours de disponibilité ne sont pas valides.'], 400);
            }
        }

        // Validates and sets the availability starting hour
        if ($request->request->has('availabilityStartingHour')) {
            $availabilityStartingHour = $request->request->get('availabilityStartingHour');

            // Format validation
            if (!preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $availabilityStartingHour)) {
                return $this->json(['error' => "Format invalide pour l'heure de début de disponibilité. Utilisez le format 00:00."], 400);
            }

            $formattedStartingHour = DateTime::createFromFormat('H:i', $availabilityStartingHour);
            $user->setAvailabilityStartingHour($formattedStartingHour);
        }

        // Validates and sets the availability ending hour
        if ($request->request->has('availabilityEndingHour')) {
            $availabilityEndingHour = $request->request->get('availabilityEndingHour');

            // Format validation
            if (!preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $availabilityEndingHour)) {
                return $this->json(['error' => "Format invalide pour l'heure de fin de disponibilité. Utilisez le format 00:00."], 400);
            }

            $formattedEndingHour = DateTime::createFromFormat('H:i', $availabilityEndingHour);
            $user->setAvailabilityEndingHour($formattedEndingHour);
        }

        $currentStartingHour = $formattedStartingHour ?? $user->getAvailabilityStartingHour();
        $currentEndingHour = $formattedEndingHour ?? $user->getAvailabilityEndingHour();

        // Logic verification
        if ($currentStartingHour && $currentEndingHour && ($currentEndingHour < $currentStartingHour)) {
            return $this->json(['error' => "L'heure de fin de disponibilité doit être postérieure à l'heure de début."], 400);
        }

        $entityManager->flush();

        return $this->json(
            [
                'message' => [
                    'type' => 'success',
                    'content' => 'Profil mis à jour avec succès !'
                ],
                'redirect_to' => '/userProfile'
            ],
            201
        );
    }

    /**
     * Validate and parse an array of days.
     *
     * This function validates and parses an array of days represented as a JSON string.
     * It checks if the input is a non-empty array and contains valid day values.
     *
     * @param string $days A JSON string representing an array of days.
     * @return array|bool|string Returns the array of valid days if input is valid, otherwise returns false.
     *                    If input is invalid, returns a JSON response with an error message.
     */
    public function validateDaysArray($days): bool|array|string
    {
        // Decode the JSON string to an associative array
        $array = json_decode($days, true);

        // Check if the decoded value is an array and not empty
        if (is_array($array) && count($array) > 0) {
            $validDays = array_unique($array);

            // Check if the day is one of the valid options (Mon, Tue, Wed, Thu, Fri, Sat, Sun)
            foreach ($validDays as $day) {
                if (!in_array($day, ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'])) {
                    return $this->json(['error' => 'Jour invalide : ' . $day], 400);
                }
            }

            // Return the array of valid days if all days are valid
            return $validDays;
        } else {

            // If the input is not a valid array or is empty, return false
            return false;
        }
    }
}

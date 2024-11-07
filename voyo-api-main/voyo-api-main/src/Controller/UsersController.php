<?php

namespace App\Controller;


use DateTime;
use Meilisearch\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use OpenApi\Attributes as OA;

class UsersController extends AbstractController
{
    /**
     * Searches for visitors based on specified criteria.
     *
     * This endpoint allows searching for visitors based on provided criteria such as address, availability date, and availability hour.
     * It is accessed via the '/api/search-visitors' endpoint using a GET request.
     * Optional parameters can be provided:
     * - 'address': The address to search for visitors.
     * - 'availabilityDate': The availability date in the format "day-month-year".
     * - 'availabilityHour': The availability hour in the format "hour:minute:second".
     *
     * If no parameters are provided, it will return all existing visitors.
     * The maximum number of results returned is 20.
     *
     * The result is an array containing information about visitors, including their name, surname, address, and city.
     * The results matching the search criteria are automatically formatted with HTML <span class="highlight"> tags for emphasis.
     *
     * @Route("/api/search-visitors", methods={"GET"})
     */
    #[OA\Response(
        response: 200,
        description: 'Returns a list of visitors (may be empty)',
    )]
    #[OA\Parameter(
        name: 'address',
        description: 'The address to search for visitors',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'availabilityDate',
        description: 'The availability date to filter visitors (format: day-month-year)',
        in: 'query',
        schema: new OA\Schema(type: 'string', format: 'date')
    )]
    #[OA\Parameter(
        name: 'availabilityHour',
        description: 'The availability hour to filter visitors (format: HH:mm:ss)',
        in: 'query',
        schema: new OA\Schema(type: 'string', format: 'time')
    )]
    #[OA\Tag(name: 'Visitors')]
    #[Route('/api/search-visitors', name: 'searchVisitors', methods: ['GET'])]
    public function searchVisitors(
        Request $request
    ): JsonResponse {
        // Retrieves the MeiliSearch URL and API keys from parameters
        $meilisearchUrl = $this->getParameter('app.meilisearchUrl');
        $meilisearchKey = $this->getParameter('app.meilisearchKey');

        // Creates a new MeiliSearch client
        $client = new Client($meilisearchUrl, $meilisearchKey);

        // Gets address and availability date from the request query parameters
        $address = $request->query->get('address') ?? '';
        $date = $request->query->get('availabilityDate');

        // Initializes an array to store day filters
        $dayFilter = [];

        // Checks if availability date is provided
        if ($date) {
            // Parses the date string into a DateTime object
            $formattedDate = DateTime::createFromFormat('d-m-Y', $date);

            // Validates the date format
            if (!($formattedDate instanceof DateTime && $formattedDate->format('d-m-Y') === $date)) {
                return $this->json(['error' => 'Invalid date format. It must be in the following format: day-month-year.'], 400);
            }

            // Gets the availability day (e.g., Mon, Tue) from the formatted date
            $availabilityDay = $formattedDate->format('D');

            // Adds the availability day filter to the day filter array
            if (!empty($availabilityDay)) {
                $dayFilter[] = "availabilityDays = " . $availabilityDay;
            }
        }

        // Gets availability hour from the request query parameters
        $availabilityHour = strtotime($request->query->get('availabilityHour'));
        $timeFilter = [];

        // Checks if availability hour is provided
        if ($availabilityHour) {
            // Validates the hour format
            if ($availabilityHour === false) {
                return $this->json(['error' => "Invalid hour format."], 400);
            }

            // Formats the availability hour to match MeiliSearch format (in seconds)
            $formattedAvailabilityHour = date("H", $availabilityHour) * 3600 +
                date("i", $availabilityHour) * 60 +
                date("s", $availabilityHour) - 3600;

            // Adds availability time filters to the time filter array
            $timeFilter[] = 'availabilityStartingHour <= ' . $formattedAvailabilityHour;
            $timeFilter[] = 'availabilityEndingHour >= ' . $formattedAvailabilityHour;
        }

        // Combines all filters into a single filter string
        $combinedFilters = implode(' AND ', array_merge(['roles = ROLE_VISITOR'], $dayFilter, $timeFilter));

        // Performs the search query on the MeiliSearch index 'User'
        $result = $client->index('User')->search($address, [
            'limit' => 20,
            'attributesToHighlight' => ['city', 'address'],
            'highlightPreTag' => '<span class="highlight">',
            'highlightPostTag' => '</span>',
            'attributesToSearchOn' => ['city', 'address'],
            'filter' => $combinedFilters
        ]);

        // Returns the search result as a JSON response
        return $this->json($result);
    }


    /**
     * Searches users or lists all users based on the provided query.
     *
     * This endpoint allows searching for users or listing all users.
     * It can be accessed using the endpoint '/api/search-users' with a GET request.
     * Optionally, you can provide the following data:
     * - 'query': The search query to filter users by name or city.
     * - 'onlyVisitors': A boolean parameter to filter users by role. If set to true, only visitors will be displayed.
     *
     * If no query is provided, all existing users will be displayed.
     * The 'onlyVisitors' parameter allows filtering users by role, displaying either only visitors or both visitors and clients.
     * The results matching the search query are automatically formatted with HTML tags <span class="highlight"> for emphasis.
     * These formatted results are available in the '_formatted' attribute of each result.
     *
     * @Route("/api/search-users", methods={"GET"})
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the search results of users',
        content: new OA\JsonContent(
            type: 'object'
        )
    )]
    #[OA\Parameter(
        name: 'query',
        description: 'The search query string',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'onlyVisitors',
        description: 'Flag to filter only visitors (optional)',
        in: 'query',
        schema: new OA\Schema(type: 'boolean')
    )]
    #[OA\Tag(name: 'Users')]
    #[Route('/api/search-users', name: 'searchUsers', methods: ['GET'])]
    public function searchUsers(
        Request $request
    ): JsonResponse {
        // Retrieve MeiliSearch URL and API key from parameters
        $meilisearchUrl = $this->getParameter('app.meilisearchUrl');
        $meilisearchKey = $this->getParameter('app.meilisearchKey');

        // Create a new MeiliSearch client
        $client = new Client($meilisearchUrl, $meilisearchKey);

        // Get the search query and filter option from the request query parameters
        $query = $request->query->get('query') ?? '';
        $onlyVisitors = $request->query->get('onlyVisitors');

        // Define search parameters
        $params = [
            'attributesToHighlight' => ['firstname', 'lastname', 'city', 'address'],
            'highlightPreTag' => '<span class="highlight">',
            'highlightPostTag' => '</span>',
            'attributesToSearchOn' => ['firstname', 'lastname', 'city', 'address'],
        ];

        // If the onlyVisitors parameter is provided, add a filter to only search for visitors
        if ($onlyVisitors) {
            $params['filter'] = 'roles = ROLE_VISITOR';
        }

        // Perform the search query on the MeiliSearch index 'User'
        $result = $client->index('User')->search($query, $params);

        // Return the search result as a JSON response
        return $this->json($result);
    }
}

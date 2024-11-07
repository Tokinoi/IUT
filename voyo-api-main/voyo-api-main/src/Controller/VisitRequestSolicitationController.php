<?php

namespace App\Controller;

use App\Entity\VisitRequestSolicitation;
use App\Repository\UserRepository;
use App\Repository\VisitRequestRepository;
use App\Repository\VisitRequestSolicitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\Routing\Annotation\Route;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;

class VisitRequestSolicitationController extends AbstractController
{
    /**
     * Lists the visit request solicitations for the current visitor.
     *
     * Only accessible by a visitor
     * Returns an empty array if no visit request solicitation is found
     *
     * Only accessible by a visitor.
     *
     * @Route("/api/visit-request-solicitations", name="visitRequestSolicitations", methods={"GET"})
     */
    #[OA\Tag(name: 'Visit request solicitation')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visit-request-solicitations', name: 'visitRequestSolicitations', methods: ['GET'])]
    public function visitRequestSolicitations(
        VisitRequestSolicitationRepository $visitRequestSolicitationRepository
    ): JsonResponse {
        // Retrieves visit request solicitations for the current user
        $visitRequestSolicitations = $visitRequestSolicitationRepository->findBy(["visitor" => $this->getUser()]);

        // Initializes an array to store the result
        $result = [];

        foreach ($visitRequestSolicitations as $visitRequestSolicitation) {
            // Extracts relevant data from the visit request solicitation
            $data = [
                'id' => $visitRequestSolicitation->getId(),
                'visitRequestId' => $visitRequestSolicitation->getVisitRequest()->getId(),
                'isAccepted' => $visitRequestSolicitation->isIsAccepted(),
            ];
            // Adds the data to the result array
            $result[] = $data;
        }

        // Returns the result as JSON response
        return $this->json($result);
    }

    /**
     * Send a visit request solicitation to a visitor.
     *
     * When a client creates a visit request, there will be a section "Recherche un visiteur".
     * When the client selects a visitor from the list, they should be able to click on a button "Demander participation".
     * Upon clicking this button, the endpoint /api/visit-request-solicitation/send should be called with the following parameters:
     * - visitRequestId: ID of the visit request
     * - visitorId: ID of the visitor
     *
     * @Route("/api/visit-request-solicitation/send", methods={"POST"})
     */
    #[OA\Parameter(
        name: 'visitorId',
        description: 'Visitor ID',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'visitRequestId',
        description: 'Visit request ID',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Visit request solicitation')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visit-request-solicitation/send', name: 'sendVisitRequestSolicitation', methods: ['POST'])]
    public function sendVisitRequestSolicitation(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        VisitRequestRepository $visitRequestRepository,
        MailerInterface $mailer,
        BodyRendererInterface $bodyRenderer,
    ): JsonResponse {
        // Retrieves the visit request and visitor from request parameters
        $visitRequest = $visitRequestRepository->find($request->request->get('visitRequestId'));
        $visitor = $userRepository->find($request->request->get('visitorId'));

        // Checks if both visit request and visitor exist
        if ($visitRequest && $visitor) {
            // Checks if the current user is the client of the visit request
            if ($this->getUser() === $visitRequest->getClient()) {
                // Creates a new visit request solicitation
                $visitRequestSolicitation = new VisitRequestSolicitation();
                $visitRequestSolicitation->setVisitor($visitor);
                $visitRequestSolicitation->setVisitRequest($visitRequest);
                $visitRequestSolicitation->setIsAccepted(null);

                // Persists the visit request solicitation
                $entityManager->persist($visitRequestSolicitation);
                $entityManager->flush();

                // Sends an email notification to the visitor
                $email = (new TemplatedEmail())
                    ->from('noreply@voyo.fr')
                    ->to($visitor->getEmail())
                    ->subject('Nouvelle demande de participation - Voyo')
                    ->htmlTemplate('emails/newVisitRequest.html.twig');
                $bodyRenderer->render($email);
                $mailer->send($email);

                // Returns success message
                return $this->json(
                    [
                        'message' => [
                            'type' => 'success',
                            'content' => 'Demande envoyée avec succès !'
                        ],
                        'redirect_to' => '/visit-request',
                        'visit-request-id' => $visitRequest->getId()
                    ],
                    201
                );
            } else {
                // Returns error message if the current user is not authorized to perform the action
                return $this->json(['error' => 'Action non autorisée.'], 400);
            }
        } else {
            // Returns error message if visit request or user is not found
            return $this->json(['error' => 'Demande de visite ou utilisateur introuvables.'], 400);
        }
    }


    /**
     * Decide on a visit request solicitation.
     *
     * A visitor can click on one of the two buttons (accept/reject) to accept or reject a visit request solicitation.
     * The endpoint related to this action is: /api/visit-request-solicitation/decide (POST) with the following parameters:
     * - id: ID of the visit request solicitation
     * - decision: "accept" or "reject"
     *
     * @Route("/api/visit-request-solicitation/decide", methods={"POST"})
     */
    #[OA\Parameter(
        name: 'id',
        description: 'ID of the visit request solicitation',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'decision',
        description: 'The decision ("accept" or "reject")',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Visit request solicitation')]
    #[Security(name: 'Bearer')]
    #[Route('/api/visit-request-solicitation/decide', name: 'decideVisitRequestSolicitation', methods: ['POST'])]
    public function decideVisitRequestSolicitation(
        Request $request,
        EntityManagerInterface $entityManager,
        VisitRequestSolicitationRepository $visitRequestSolicitationRepository
    ): JsonResponse {
        // Retrieves the visit request solicitation and decision from request parameters
        $visitRequestSolicitation = $visitRequestSolicitationRepository->find($request->request->get('id'));
        $decision = $request->request->get('decision');

        // Checks if both visit request solicitation and decision exist
        if ($visitRequestSolicitation && $decision) {
            // Checks if the decision is valid (accept or reject)
            if (in_array($decision, ["accept", "reject"])) {
                if ($decision == "accept") {
                    // Accepts the visit request solicitation
                    $visitRequestSolicitation->setIsAccepted(true);
                    $visitRequest = $visitRequestSolicitation->getVisitRequest();
                    $visitRequest->setVisitor($this->getUser());

                    // Removes all other visit request solicitations for the same visit request
                    $visitRequestSolicitations = $visitRequestSolicitationRepository->findBy(["visitRequest" => $visitRequest]);
                    foreach ($visitRequestSolicitations as $uselessVisitRequestSolicitation) {
                        $entityManager->remove($uselessVisitRequestSolicitation);
                    }
                } else {
                    // Rejects the visit request solicitation
                    $visitRequestSolicitation->setIsAccepted(false);
                }

                // Persists the changes
                $entityManager->persist($visitRequestSolicitation);
                $entityManager->flush();

                // Returns success message
                return $this->json(
                    [
                        'message' => [
                            'type' => 'success',
                            'content' => 'Decision effectuée avec succès !'
                        ],
                        'redirect_to' => '/visit-request',
                        'visit-request-id' => $visitRequestSolicitation->getVisitRequest()->getId()
                    ],
                    201
                );
            } else {
                // Return an error message for incorrect decision
                return $this->json(['error' => 'Decision incorrecte.'], 400);
            }
        } else {
            // Returns an error message if visit request solicitation or decision is not found
            return $this->json(['error' => 'Demande de visite ou decision introuvables.'], 400);
        }
    }
}

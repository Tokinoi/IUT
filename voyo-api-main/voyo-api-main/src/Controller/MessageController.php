<?php

namespace App\Controller;

use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use App\Entity\Message;
use App\Repository\UserRepository;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;

class MessageController extends AbstractController
{
    /**
     * Send a message to a recipient.
     *
     * This endpoint allows users to send messages to each other. It expects the following parameters:
     * - 'senderId': The ID of the sender.
     * - 'recipientId': The ID of the recipient.
     * - 'content': The content of the message.
     *
     * If the sender or recipient is not found, an error message will be returned:
     * { "error" : "Sender or recipient not found." }
     *
     * Upon successful sending of the message, a success message will be returned:
     * { "message": "Message sent successfully." }
     *
     * @Route("/api/messages/send", name="send_message", methods={"POST"})
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the a success/error message',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Message::class, groups: ['full']))
        )
    )]
    #[OA\Parameter(
        name: 'senderId',
        description: 'The ID of the sender',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'recipientId',
        description: 'The ID of the recipient',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'content',
        description: 'The text content of the message',
        in: 'query',
        schema: new OA\Schema(type: 'text')
    )]
    #[OA\Tag(name: 'Messages')]
    #[Security(name: 'Bearer')]
    #[Route('/api/messages/send', name: 'send_message', methods: ['POST'])]
    public function sendMessage(
        Request $request,
        SerializerInterface $serializer,
        HubInterface $hub,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Get sender and recipient
        $sender = $userRepository->find($request->request->get('senderId'));
        $recipient = $userRepository->find($request->request->get('recipientId'));
        $content = $request->request->get('content');

        if (!$sender || !$recipient) {
            return $this->json(['error' => 'Sender or recipient not found.'], 404);
        }

        // Create and persist message to the DB
        $message = new Message();
        $message->setSender($sender);
        $message->setRecipent($recipient);
        $message->setTimestamp(new \DateTime());
        $message->setContent($content);

        $entityManager->persist($message);
        $entityManager->flush();

        // Publish message to recipient
        $update = new Update(
            sprintf('/messages/%d', $recipient->getId()),
            $serializer->serialize($message, 'json')
        );

        $hub->publish($update);

        return $this->json(['message' => 'Message sent successfully.'], 201);
    }

    /**
     * Get all messages sent or received by the authenticated user.
     *
     * This endpoint retrieves all messages sent or received by the authenticated user.
     * It returns an array of messages with the following details:
     * - 'sender': An array containing the ID and display name of the sender.
     * - 'recipient': An array containing the ID and display name of the recipient.
     * - 'timestamp': The timestamp of the message.
     * - 'content': The content of the message.
     *
     * @Route("/api/messages", name="get_messages", methods={"GET"})
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the a success/error message',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Message::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'Messages')]
    #[Security(name: 'Bearer')]
    #[Route('/api/messages', name: 'get_messages', methods: ['GET'])]
    public function getMessages(MessageRepository $messageRepository): Response
    {
        // Retrieve messages where the current user is either the sender or the recipient
        $recipientMessages = $messageRepository->createQueryBuilder('m')
            ->where('m.sender = :user OR m.recipent = :user')
            ->setParameter('user', $this->getUser()->getId())
            ->getQuery()
            ->getResult();

        // Formats the retrieved messages
        $messages = [];
        foreach ($recipientMessages as $message) {
            $messages[] = [
                'sender' => [
                    'id' => $message->getSender()->getId(),
                    'displayName' => $message->getSender()->getDisplayName(),
                ],
                'recipient' => [
                    'id' => $message->getRecipent()->getId(),
                    'displayName' => $message->getRecipent()->getDisplayName(),
                ],
                'timestamp' => $message->getTimestamp()->format('Y-m-d H:i:s'),
                'content' => $message->getContent(),
            ];
        }

        // Returns the formatted messages as JSON response
        return $this->json($messages, 200, [], ['groups' => 'message:read']);
    }
}
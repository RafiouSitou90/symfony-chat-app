<?php

namespace App\Controller;

use App\Entity\Conversations;
use App\Entity\ConversationsUsers;
use App\Entity\Messages;
use App\Entity\Users;
use App\Repository\ConversationsUsersRepository;
use App\Repository\MessagesRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class MessagesController
 * @package App\Controller

 * @Route("/messages", name="app_messages_")
 */
class MessagesController extends AbstractController
{
    /**
     * @var string[]
     */
    private const ATTRIBUTES_TO_SERIALIZE = ['id', 'content', 'createdAt'];
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;
    /**
     * @var UsersRepository
     */
    private UsersRepository $usersRepository;
    /**
     * @var ConversationsUsersRepository
     */
    private ConversationsUsersRepository $conversationsUsersRepository;
    /**
     * @var MessagesRepository
     */
    private MessagesRepository $messagesRepository;

    /**
     * MessagesController constructor.
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UsersRepository $usersRepository
     * @param ConversationsUsersRepository $conversationsUsersRepository
     * @param MessagesRepository $messagesRepository
     */
    public function __construct (
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UsersRepository $usersRepository,
        ConversationsUsersRepository $conversationsUsersRepository,
        MessagesRepository $messagesRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->usersRepository = $usersRepository;
        $this->conversationsUsersRepository = $conversationsUsersRepository;
        $this->messagesRepository = $messagesRepository;
    }

    /**
     * @Route("/{id}", name="index", methods={"GET"})
     * @param Request $request
     * @param Conversations $conversation
     * @return JsonResponse
     */
    public function index (Request $request, Conversations $conversation): JsonResponse
    {
        $this->denyAccessUnlessGranted('view', $conversation);

        $messages = $this->messagesRepository->findMessagesByConversationId(
            $conversation->getId()
        );

        return $this->json($messages, Response::HTTP_OK, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE,
            'circular_reference_handler' => fn ($object) => $object->getId(),
        ]);
    }

    /**
     * @Route("/{id}", name="create", methods={"POST"})
     * @param Request $request
     * @param Conversations $conversation
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function create (Request $request, Conversations $conversation)
    {
        /** @var Users $user */
//        $user = $this->getUser();
        $user = $this->usersRepository->find('dc2b0f21-cad6-11ea-806c-00ff004d54d3'); // $this->getUser();
//        $user = $this->usersRepository->find('f9a0dc94-cad5-11ea-806c-00ff004d54d3'); // $this->getUser();

        /** @var ConversationsUsers $recipient */
        $recipient = $this->conversationsUsersRepository->findByConversationIdAndUserId(
            $conversation->getId(),
            $user->getId()
        );

        $content = $request->get('content', null);

        $message = (new Messages())
            ->setUser($user)
            ->setContent($content)
        ;

        $conversation->addMessage($message);
        $conversation->setLastMessage($message);

        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($message);
            $this->entityManager->persist($conversation);
            $this->entityManager->flush();
            $this->entityManager->commit();

        }catch (Exception $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }


        $messageSerialized = $this->serializer->serialize($message, 'json', [
            'attributes' => [...self::ATTRIBUTES_TO_SERIALIZE, 'conversation' => ['id']]
        ]);

        $update = new Update(
            [
                sprintf("/conversations/%s", $conversation->getId()),
                sprintf("/conversations/%s", $recipient->getUser()->getUsername()),
            ],
            $messageSerialized,
            true,
        );

        $this->dispatchMessage($update);

        return $this->json($message, Response::HTTP_CREATED, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);
    }
}

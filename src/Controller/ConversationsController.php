<?php

namespace App\Controller;

use App\Entity\Conversations;
use App\Entity\ConversationsUsers;
use App\Entity\Users;
use App\Repository\ConversationsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConversationsController
 * @package App\Controller
 *
 * @IsGranted("ROLE_USER")
 * @Route("/conversations", name="app_conversations_")
 */
class ConversationsController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var ConversationsRepository
     */
    private ConversationsRepository $conversationsRepository;
    /**
     * @var UsersRepository
     */
    private UsersRepository $usersRepository;

    /**
     * ConversationsController constructor.
     * @param EntityManagerInterface $entityManager
     * @param ConversationsRepository $conversationsRepository
     * @param UsersRepository $usersRepository
     */
    public function __construct (
        EntityManagerInterface $entityManager,
        ConversationsRepository $conversationsRepository,
        UsersRepository $usersRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->conversationsRepository = $conversationsRepository;
        $this->usersRepository = $usersRepository;
    }

    /**
     * @Route("", name="create", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function create (Request $request): JsonResponse
    {
        /** @var Users $current_user */
        $current_user = $this->getUser();
        $second_user = $request->request->get('second_user', null);

        /** @var Users|null $second_user */
        $second_user = $this->usersRepository->find($second_user);

        if (is_null($second_user)) {
            throw new NotFoundHttpException('User not found');
        }

        if ($second_user->getId() === $current_user->getId()) {
            throw new BadRequestException('You cannot create conversation with yourself');
        }

        $conversation = $this->conversationsRepository->findConversationByUsers(
            $current_user->getId(),
            $second_user->getId()
        );

        if (count($conversation)) {
            throw new BadRequestException('The conversation already exists');
        }

        $conversation = new Conversations();

        $firstParticipantConversationUser = (new ConversationsUsers())
            ->setUser($current_user)
            ->setConversation($conversation)
        ;

        $secondParticipantConversationUser = (new ConversationsUsers())
            ->setUser($second_user)
            ->setConversation($conversation)
        ;

        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->persist($conversation);
            $this->entityManager->persist($firstParticipantConversationUser);
            $this->entityManager->persist($secondParticipantConversationUser);
            $this->entityManager->flush();
            $this->entityManager->commit();

        } catch (Exception $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $this->json(['id' => $conversation->getId()], Response::HTTP_CREATED, [], []);
    }

    /**
     * @Route("", name="show", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function show (Request $request): JsonResponse
    {
        /** @var Users $current_user */
        $current_user = $this->getUser();

        $conversations = $this->conversationsRepository->findConversationsByUserId($current_user->getId());

//        $hubUrl = $this->getParameter('mercure.default_hub');

//        $this->addLink($request, new Link('mercure', $hubUrl));

        return $this->json($conversations, Response::HTTP_OK, [], []);
    }

}

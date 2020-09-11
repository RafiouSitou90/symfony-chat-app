<?php

namespace App\Security\Voter;

use App\Entity\Conversations;
use App\Entity\Users;
use App\Repository\ConversationsRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ConversationVoter extends Voter
{
    private const VIEW = 'view';
    private const EDIT = 'edit';
    private const ADD = 'add';
    /**
     * @var ConversationsRepository
     */
    private ConversationsRepository $conversationsRepository;

    /**
     * ConversationVoter constructor.
     * @param ConversationsRepository $conversationsRepository
     */
    public function __construct (ConversationsRepository $conversationsRepository)
    {
        $this->conversationsRepository = $conversationsRepository;
    }
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::EDIT, self::VIEW])) {
            return false;
        }

        if (!$subject instanceof Conversations) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Users $user */
        $user = $token->getUser();;
        switch ($attribute) {
            case self::VIEW:
                $vote = $this->conversationsRepository->checkIfUserIsParticipant(
                    $subject->getId(),
                    $user->getId(),
                );
                break;
            default:
                $vote = false;
        }

        return $vote;
    }
}

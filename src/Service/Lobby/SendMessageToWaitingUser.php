<?php

namespace App\Service\Lobby;

use App\Entity\LobbyWaitungUser;
use App\Entity\PredefinedLobbyMessages;
use App\Entity\Rooms;
use App\Entity\User;
use App\Service\ThemeService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class SendMessageToWaitingUser
{
    private $isAllowedToCreateCustom;

    public function __construct(
        private EntityManagerInterface        $entityManager,
        private ToParticipantWebsocketService $toParticipantWebsocketService,
        private ThemeService                  $themeService,
        private LoggerInterface               $logger,
    )
    {
        $this->isAllowedToCreateCustom = $this->themeService->getApplicationProperties('LAF_LOBBY_ALLOW_CUSTOM_MESSAGES');
    }

    public function sendMessageToAllWaitingUser($message, User $user, Rooms $rooms): array
    {
        $counter = 0;
        $success = true;
        foreach ($rooms->getLobbyWaitungUsers() as $data) {
            if ($this->sendMessage($data->getUid(), $message, $user) === true) {
                $counter++;
            }else{
                $success = false;
            };
        }
        return array('counter'=>$counter,'success'=>$success);
    }

    public function sendMessage($uid, $message, User $user): bool
    {
        $waitingUser = $this->entityManager->getRepository(LobbyWaitungUser::class)->findOneBy(array('uid' => $uid));
        if (!$waitingUser) {
            $this->logger->error('NO user found for uid', array('uid' => $uid));
            return false;
        }
        $lobbyModerator = $user->getPermissionForRoom($waitingUser->getRoom())->getLobbyModerator();
        if ($user === $waitingUser->getRoom()->getModerator() || $lobbyModerator) {
            if (is_int($message)) {
                $this->logger->debug('Send Message from id', array('id' => $message));
                $res = $this->createMesagefromId($message);

            } else {
                $this->logger->debug('Send Message from string', array('id' => $message));
                $res = $this->createMessageFromString($message, $this->isAllowedToCreateCustom);
            }
            if ($res) {
                $this->logger->debug('Send Message via websocket', array('uid' => $waitingUser->getUid(), 'message' => $res));
                $this->toParticipantWebsocketService->sendMessage($waitingUser, $res, $user->getFormatedName($this->themeService->getApplicationProperties('laf_showNameFrontend')));
            }

            return (bool)$res;
        } else {
            $this->logger->error('USer tried to send message where he has no acess to', array('USer-uid' => $user->getUsername()));
            return false;
        }


    }

    public function createMesagefromId($id): ?string
    {
        $message = $this->entityManager->getRepository(PredefinedLobbyMessages::class)->findOneBy(array('id' => $id, 'active' => true));
        if (!$message) {
            $this->logger->debug('Fetch message from id', array('message' => $id));
            return null;
        }
        $this->logger->debug('Fetch message from id', array('message' => $message->getText()));
        return $message->getText();
    }

    public function createMessageFromString($message, int $allowCreating): ?string
    {
        if ($allowCreating === 1) {
            $this->logger->debug('We create a custom message from a string');
            return $message;
        }
        $this->logger->debug('No custom messages are allowed');
        return null;
    }

}
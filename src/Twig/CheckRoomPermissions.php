<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Checklist;
use App\Entity\MyUser;
use App\Entity\Rooms;
use App\Entity\RoomsUser;
use App\Entity\Server;
use App\Entity\User;
use App\Service\LicenseService;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function GuzzleHttp\Psr7\str;

class CheckRoomPermissions extends AbstractExtension
{



    private $em;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('roomPermissions', [$this, 'roomPermissions']),
        ];
    }
    public function roomPermissions(User $user, Rooms $rooms):?RoomsUser
    {
      $permissions = $this->em->getRepository(RoomsUser::class)->findOneBy(array('user'=>$user, 'room'=>$rooms));
     if(!$permissions){
         $permissions = new RoomsUser();
     }
      return $permissions;
    }

}
<?php


namespace App\Service\ldap;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Ldap\Entry;

class LdapUserService
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * This function retrieves the user
     * @param Entry $entry
     * @param string $userNameAttribute
     * @param $mapper
     * @return User|object
     */
    public function retrieveUserfromDatabase(Entry $entry, string $userNameAttribute, $mapper, $url)
    {
        $uid = $entry->getAttribute($userNameAttribute)[0];
        $email = $entry->getAttribute($mapper['email'])[0];
        $firstName = $entry->getAttribute($mapper['firstName'])[0];
        $lastName = $entry->getAttribute($mapper['lastName'])[0];

        $user = $this->em->getRepository(User::class)->findOneBy(array('username' => $uid));

        if (!$user) {
            $user = new User();
            $user->setUsername($uid);
            $user->setCreatedAt(new \DateTime());
            $user->setUid(md5(uniqid()));
            $user->setUuid(md5(uniqid()));
            $user->setLdapDn($entry->getDn());
            $user->setLdapHost($url);
        }
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    /**
     * This function connects all users in the Database with the adressbook of all available other users.
     * So everyon can search for everyone in the whole jitsi-admin system
     *
     */
    public function connectUserwithAllUSersInAdressbock(){
        $allUSer = $this->em->getRepository(User::class)->findAll();
        foreach ($allUSer as $data){
            foreach ($allUSer as $data2){
                $data->addAddressbook($data2);
            }
            $this->em->persist($data);
        }
        $this->em->flush();
    }
}
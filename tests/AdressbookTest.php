<?php

namespace App\Tests;

use App\Repository\AddressGroupRepository;
use App\Repository\UserRepository;
use App\Service\ParticipantSearchService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AdressbookTest extends KernelTestCase
{
    public const SEARCHUSERPOSITIVE = [
        'test@local2.de',
        'local2.de',
        'test',
        'Test',
        'User',
        '1234',
        'Test1'
    ];

    public function testfindUserandGroups(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        $userRepo = self::$container->get(UserRepository::class);
        $user = $userRepo->findOneBy(array('email' => 'test@local.de'));
        $userfind = $userRepo->findOneBy(array('email' => 'test@local2.de'));
        $groupRepo = $this->getContainer()->get(AddressGroupRepository::class);
        $groupFind = $groupRepo->findOneBy(array('name' => 'Testgruppe'));
        $userRepo->findMyUserByEmail('test@local2.de', $user);
        foreach (self::SEARCHUSERPOSITIVE as $data){
            self::assertEquals($userfind, $userRepo->findMyUserByEmail($data, $user)[0]);
        }
        self::assertEquals(0, sizeof($userRepo->findMyUserByEmail('User12', $user)));
        self::assertEquals($userfind, $userRepo->findMyUserByEmail('Test1', $user)[0]);
        self::assertEquals($groupFind, $groupRepo->findMyAddressBookGroupsByName('Testgr', $user)[0]);
        self::assertEquals($groupFind, $groupRepo->findMyAddressBookGroupsByName('Test', $user)[0]);
        self::assertEquals(0, sizeof($groupRepo->findMyAddressBookGroupsByName('Testwe', $user)));
    }
    public function testgenerateUSer(): void
    {
        $kernel = self::bootKernel();
        $searchService = $this->getContainer()->get(ParticipantSearchService::class);
        $this->assertSame('test', $kernel->getEnvironment());
        $userRepo = self::$container->get(UserRepository::class);
        $user = $userRepo->findOneBy(array('email' => 'test@local.de'));
        $userfind = $userRepo->findOneBy(array('email' => 'test@local2.de'));
        $groupRepo = $this->getContainer()->get(AddressGroupRepository::class);
        $groupFind = $groupRepo->findOneBy(array('name' => 'Testgruppe'));
        $userRepo->findMyUserByEmail('test@local2.de', $user);
        $string = 'test';
        $userArr = $userRepo->findMyUserByEmail($string, $user);
        $res = $searchService->generateUserwithEmptyUser($userArr,$string);
        $this->assertEquals(array(
            array('name'=>"Test1, 1234, User, Test",'id'=>"test2@local.de"),
            array('name'=>', , , ','id'=>"test@local3.de")
        ),$res);
        $string = '1234';
        $userArr = $userRepo->findMyUserByEmail($string, $user);
        $res = $searchService->generateUserwithEmptyUser($userArr,$string);
        $this->assertEquals(array(
            array('name'=>"Test1, 1234, User, Test",'id'=>"test2@local.de"),
        ),$res);
        $string = 'asdf';
        $userArr = $userRepo->findMyUserByEmail($string, $user);
        $res = $searchService->generateUserwithEmptyUser($userArr,$string);
        $this->assertEquals(array(
            array('name'=>"asdf",'id'=>"asdf"),
        ),$res);
        $string = 'TEst1';
        $userArr = $userRepo->findMyUserByEmail($string, $user);
        $res = $searchService->generateUserwithEmptyUser($userArr,$string);
        $this->assertEquals(array(
            array('name'=>"Test1, 1234, User, Test",'id'=>"test2@local.de"),
        ),$res);

    }
    public function testNoUserFoundandGenerate(): void
    {
        $kernel = self::bootKernel();
        $searchService = $this->getContainer()->get(ParticipantSearchService::class);
        $this->assertSame('test', $kernel->getEnvironment());
        $userRepo = self::$container->get(UserRepository::class);
        $user = $userRepo->findOneBy(array('email' => 'test@local.de'));
        $userfind = $userRepo->findOneBy(array('email' => 'test@local2.de'));
        $groupRepo = $this->getContainer()->get(AddressGroupRepository::class);
        $groupFind = $groupRepo->findOneBy(array('name' => 'Testgruppe'));
        $userRepo->findMyUserByEmail('test@local2.de', $user);
        $string = 'asdf';
        $userArr = $userRepo->findMyUserByEmail($string, $user);
        $res = $searchService->generateUserwithEmptyUser($userArr,$string);
        $this->assertEquals(array(
            array('name'=>$string, 'id'=>$string)
        ),$res);

    }
    public function testUserFoundandGenerate(): void
    {
        $kernel = self::bootKernel();
        $searchService = $this->getContainer()->get(ParticipantSearchService::class);
        $this->assertSame('test', $kernel->getEnvironment());
        $userRepo = self::$container->get(UserRepository::class);
        $user = $userRepo->findOneBy(array('email' => 'test@local.de'));
        $userRepo->findMyUserByEmail('test@local2.de', $user);
        $string = 'test';
        $userArr = $userRepo->findMyUserByEmail($string, $user);
        $res = $searchService->generateUserwithoutEmptyUser($userArr);
        $this->assertEquals(array(
            array('name'=>"Test1, 1234, User, Test",'id'=>"test2@local.de"),
            array('name'=>', , , ','id'=>"test@local3.de")
        ),$res);
        $string = '1234';
        $userArr = $userRepo->findMyUserByEmail($string, $user);
        $res = $searchService->generateUserwithoutEmptyUser($userArr);
        $this->assertEquals(array(
            array('name'=>"Test1, 1234, User, Test",'id'=>"test2@local.de"),
        ),$res);
    }
    public function testnoUSerfoundNoGenerate(): void
    {
        $kernel = self::bootKernel();
        $searchService = $this->getContainer()->get(ParticipantSearchService::class);
        $this->assertSame('test', $kernel->getEnvironment());
        $userRepo = self::$container->get(UserRepository::class);
        $user = $userRepo->findOneBy(array('email' => 'test@local.de'));
        $userRepo->findMyUserByEmail('test@local2.de', $user);
        $string = 'asdf';
        $userArr = $userRepo->findMyUserByEmail($string, $user);
        $res = $searchService->generateUserwithoutEmptyUser($userArr,$string);
        $this->assertEquals(array(
        ),$res);
    }
    public function testUserFoundNoGenerate(): void
    {
        $kernel = self::bootKernel();
        $searchService = $this->getContainer()->get(ParticipantSearchService::class);
        $this->assertSame('test', $kernel->getEnvironment());
        $userRepo = self::$container->get(UserRepository::class);
        $user = $userRepo->findOneBy(array('email' => 'test@local.de'));
        $userfind = $userRepo->findOneBy(array('email' => 'test@local2.de'));
        $groupRepo = $this->getContainer()->get(AddressGroupRepository::class);
        $groupFind = $groupRepo->findOneBy(array('name' => 'Testgruppe'));
        $userRepo->findMyUserByEmail('test@local2.de', $user);
        $string = 'test';
        $userArr = $userRepo->findMyUserByEmail($string, $user);
        $res = $searchService->generateUserwithoutEmptyUser($userArr);
        $this->assertEquals(array(
            array('name'=>"Test1, 1234, User, Test",'id'=>"test2@local.de"),
            array('name'=>', , , ','id'=>"test@local3.de")
        ),$res);
        $string = '1234';
        $userArr = $userRepo->findMyUserByEmail($string, $user);
        $res = $searchService->generateUserwithoutEmptyUser($userArr);
        $this->assertEquals(array(
            array('name'=>"Test1, 1234, User, Test",'id'=>"test2@local.de"),
        ),$res);

    }

    public function testgroupFound(): void
    {
        $kernel = self::bootKernel();
        $searchService = $this->getContainer()->get(ParticipantSearchService::class);
        $this->assertSame('test', $kernel->getEnvironment());
        $userRepo = self::$container->get(UserRepository::class);
        $user = $userRepo->findOneBy(array('email' => 'test@local.de'));
        $groupRepo = $this->getContainer()->get(AddressGroupRepository::class);
        $userRepo->findMyUserByEmail('test@local2.de', $user);
        $string = 'test';
        $userGroup = $groupRepo->findMyAddressBookGroupsByName($string, $user);
        $res = $searchService->generateGroup($userGroup);
        $this->assertEquals(array(
            array('name' => "Testgruppe", 'user' => "test2@local.de\ntest@local3.de"),
        ), $res);
        $string = 'Testgruppe';
        $userGroup = $groupRepo->findMyAddressBookGroupsByName($string, $user);
        $res = $searchService->generateGroup($userGroup);
        $this->assertEquals(array(
            array('name' => "Testgruppe", 'user' => "test2@local.de\ntest@local3.de"),
        ), $res);
        $string = 'testio';
        $userGroup = $groupRepo->findMyAddressBookGroupsByName($string, $user);
        $res = $searchService->generateGroup($userGroup);
        $this->assertEquals(array(), $res);

    }

        public function testNogroupFound(): void
    {
        $kernel = self::bootKernel();
        $searchService = $this->getContainer()->get(ParticipantSearchService::class);
        $this->assertSame('test', $kernel->getEnvironment());
        $userRepo = self::$container->get(UserRepository::class);
        $user = $userRepo->findOneBy(array('email' => 'test@local.de'));
        $groupRepo = $this->getContainer()->get(AddressGroupRepository::class);
        $userRepo->findMyUserByEmail('test@local2.de', $user);
        $string = 'test';
        $userGroup = $groupRepo->findMyAddressBookGroupsByName($string, $user);
        $res = $searchService->generateGroup($userGroup);
        $string = 'testio';
        $userGroup = $groupRepo->findMyAddressBookGroupsByName($string, $user);
        $res = $searchService->generateGroup($userGroup);
        $this->assertEquals(array(
        ),$res);

    }
}

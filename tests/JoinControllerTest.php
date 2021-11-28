<?php

namespace App\Tests;

use App\Repository\RoomsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JoinControllerTest extends WebTestCase
{
    public function testjoinRoomBrowser(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('test@local.de');
        $roomRepo = self::getContainer()->get(RoomsRepository::class);
        $room = $roomRepo->findOneBy(array('name'=>'TestMeeting: 1'));
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/room/join/b/'.$room->getId());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        self::assertTrue($client->getResponse()->isRedirect('https://meet.jit.si2/123456781?jwt=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJqaXRzaV9hZG1pbiIsImlzcyI6ImppdHNpSWQiLCJzdWIiOiJtZWV0LmppdC5zaTIiLCJyb29tIjoiMTIzNDU2NzgxIiwiY29udGV4dCI6eyJ1c2VyIjp7Im5hbWUiOiJVc2VyLCBUZXN0LCB0ZXN0QGxvY2FsLmRlIn19LCJtb2RlcmF0b3IiOnRydWV9.rgoK2HJlevbuRz1M3cIrkmJSARhQ6addjyaBG6zP4qU#config.subject=%22TestMeeting: 1%22'));
    }
    public function testjoinRoomApp(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByUsername('test@local.de');
        $roomRepo = self::getContainer()->get(RoomsRepository::class);
        $room = $roomRepo->findOneBy(array('name'=>'TestMeeting: 1'));
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/room/join/a/'.$room->getId());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        self::assertTrue($client->getResponse()->isRedirect('jitsi-meet://meet.jit.si2/123456781?jwt=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJqaXRzaV9hZG1pbiIsImlzcyI6ImppdHNpSWQiLCJzdWIiOiJtZWV0LmppdC5zaTIiLCJyb29tIjoiMTIzNDU2NzgxIiwiY29udGV4dCI6eyJ1c2VyIjp7Im5hbWUiOiJVc2VyLCBUZXN0LCB0ZXN0QGxvY2FsLmRlIn19LCJtb2RlcmF0b3IiOnRydWV9.rgoK2HJlevbuRz1M3cIrkmJSARhQ6addjyaBG6zP4qU#config.subject=%22TestMeeting: 1%22'));
    }
}

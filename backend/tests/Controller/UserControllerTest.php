<?php

namespace App\Test\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $repository;
    private string $path = '/user/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(User::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('User index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'user[name]' => 'Testing',
            'user[surname]' => 'Testing',
            'user[email]' => 'Testing',
            'user[phone]' => 'Testing',
            'user[password]' => 'Testing',
            'user[avatar]' => 'Testing',
            'user[bio]' => 'Testing',
            'user[created_at]' => 'Testing',
            'user[trip_count]' => 'Testing',
            'user[passengerTrips]' => 'Testing',
        ]);

        self::assertResponseRedirects('/user/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setName('My Title');
        $fixture->setSurname('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPhone('My Title');
        $fixture->setPassword('My Title');
        $fixture->setAvatar('My Title');
        $fixture->setBio('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setTrip_count('My Title');
        $fixture->setPassengerTrips('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('User');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setName('My Title');
        $fixture->setSurname('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPhone('My Title');
        $fixture->setPassword('My Title');
        $fixture->setAvatar('My Title');
        $fixture->setBio('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setTrip_count('My Title');
        $fixture->setPassengerTrips('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'user[name]' => 'Something New',
            'user[surname]' => 'Something New',
            'user[email]' => 'Something New',
            'user[phone]' => 'Something New',
            'user[password]' => 'Something New',
            'user[avatar]' => 'Something New',
            'user[bio]' => 'Something New',
            'user[created_at]' => 'Something New',
            'user[trip_count]' => 'Something New',
            'user[passengerTrips]' => 'Something New',
        ]);

        self::assertResponseRedirects('/user/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getSurname());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getPhone());
        self::assertSame('Something New', $fixture[0]->getPassword());
        self::assertSame('Something New', $fixture[0]->getAvatar());
        self::assertSame('Something New', $fixture[0]->getBio());
        self::assertSame('Something New', $fixture[0]->getCreated_at());
        self::assertSame('Something New', $fixture[0]->getTrip_count());
        self::assertSame('Something New', $fixture[0]->getPassengerTrips());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new User();
        $fixture->setName('My Title');
        $fixture->setSurname('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPhone('My Title');
        $fixture->setPassword('My Title');
        $fixture->setAvatar('My Title');
        $fixture->setBio('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setTrip_count('My Title');
        $fixture->setPassengerTrips('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/user/');
    }
}

<?php

namespace App\Test\Controller;

use App\Entity\Trip;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TripControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TripRepository $repository;
    private string $path = '/trip/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Trip::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Trip index');

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
            'trip[max_passenger]' => 'Testing',
            'trip[date_start]' => 'Testing',
            'trip[finished]' => 'Testing',
            'trip[cancelled]' => 'Testing',
            'trip[driver]' => 'Testing',
            'trip[start_address]' => 'Testing',
            'trip[destination_address]' => 'Testing',
            'trip[passenger]' => 'Testing',
        ]);

        self::assertResponseRedirects('/trip/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Trip();
        $fixture->setMax_passenger('My Title');
        $fixture->setDate_start('My Title');
        $fixture->setFinished('My Title');
        $fixture->setCancelled('My Title');
        $fixture->setDriver('My Title');
        $fixture->setStart_address('My Title');
        $fixture->setDestination_address('My Title');
        $fixture->setPassenger('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Trip');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Trip();
        $fixture->setMax_passenger('My Title');
        $fixture->setDate_start('My Title');
        $fixture->setFinished('My Title');
        $fixture->setCancelled('My Title');
        $fixture->setDriver('My Title');
        $fixture->setStart_address('My Title');
        $fixture->setDestination_address('My Title');
        $fixture->setPassenger('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'trip[max_passenger]' => 'Something New',
            'trip[date_start]' => 'Something New',
            'trip[finished]' => 'Something New',
            'trip[cancelled]' => 'Something New',
            'trip[driver]' => 'Something New',
            'trip[start_address]' => 'Something New',
            'trip[destination_address]' => 'Something New',
            'trip[passenger]' => 'Something New',
        ]);

        self::assertResponseRedirects('/trip/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getMax_passenger());
        self::assertSame('Something New', $fixture[0]->getDate_start());
        self::assertSame('Something New', $fixture[0]->getFinished());
        self::assertSame('Something New', $fixture[0]->getCancelled());
        self::assertSame('Something New', $fixture[0]->getDriver());
        self::assertSame('Something New', $fixture[0]->getStart_address());
        self::assertSame('Something New', $fixture[0]->getDestination_address());
        self::assertSame('Something New', $fixture[0]->getPassenger());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Trip();
        $fixture->setMax_passenger('My Title');
        $fixture->setDate_start('My Title');
        $fixture->setFinished('My Title');
        $fixture->setCancelled('My Title');
        $fixture->setDriver('My Title');
        $fixture->setStart_address('My Title');
        $fixture->setDestination_address('My Title');
        $fixture->setPassenger('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/trip/');
    }
}

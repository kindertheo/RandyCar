<?php

namespace App\Test\Controller;

use App\Entity\Car;
use App\Repository\CarRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CarControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CarRepository $repository;
    private string $path = '/car/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Car::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Car index');

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
            'car[brand]' => 'Testing',
            'car[model]' => 'Testing',
            'car[color]' => 'Testing',
            'car[seat_number]' => 'Testing',
            'car[license_plate]' => 'Testing',
            'car[owner]' => 'Testing',
            'car[fuel]' => 'Testing',
        ]);

        self::assertResponseRedirects('/car/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Car();
        $fixture->setBrand('My Title');
        $fixture->setModel('My Title');
        $fixture->setColor('My Title');
        $fixture->setSeat_number('My Title');
        $fixture->setLicense_plate('My Title');
        $fixture->setOwner('My Title');
        $fixture->setFuel('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Car');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Car();
        $fixture->setBrand('My Title');
        $fixture->setModel('My Title');
        $fixture->setColor('My Title');
        $fixture->setSeat_number('My Title');
        $fixture->setLicense_plate('My Title');
        $fixture->setOwner('My Title');
        $fixture->setFuel('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'car[brand]' => 'Something New',
            'car[model]' => 'Something New',
            'car[color]' => 'Something New',
            'car[seat_number]' => 'Something New',
            'car[license_plate]' => 'Something New',
            'car[owner]' => 'Something New',
            'car[fuel]' => 'Something New',
        ]);

        self::assertResponseRedirects('/car/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getBrand());
        self::assertSame('Something New', $fixture[0]->getModel());
        self::assertSame('Something New', $fixture[0]->getColor());
        self::assertSame('Something New', $fixture[0]->getSeat_number());
        self::assertSame('Something New', $fixture[0]->getLicense_plate());
        self::assertSame('Something New', $fixture[0]->getOwner());
        self::assertSame('Something New', $fixture[0]->getFuel());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Car();
        $fixture->setBrand('My Title');
        $fixture->setModel('My Title');
        $fixture->setColor('My Title');
        $fixture->setSeat_number('My Title');
        $fixture->setLicense_plate('My Title');
        $fixture->setOwner('My Title');
        $fixture->setFuel('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/car/');
    }
}

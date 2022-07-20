<?php

namespace App\Test\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotificationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private NotificationRepository $repository;
    private string $path = '/notification/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Notification::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Notification index');

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
            'notification[object]' => 'Testing',
            'notification[content]' => 'Testing',
            'notification[created_at]' => 'Testing',
            'notification[read]' => 'Testing',
            'notification[receiver]' => 'Testing',
        ]);

        self::assertResponseRedirects('/notification/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Notification();
        $fixture->setObject('My Title');
        $fixture->setContent('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setRead('My Title');
        $fixture->setReceiver('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Notification');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Notification();
        $fixture->setObject('My Title');
        $fixture->setContent('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setRead('My Title');
        $fixture->setReceiver('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'notification[object]' => 'Something New',
            'notification[content]' => 'Something New',
            'notification[created_at]' => 'Something New',
            'notification[read]' => 'Something New',
            'notification[receiver]' => 'Something New',
        ]);

        self::assertResponseRedirects('/notification/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getObject());
        self::assertSame('Something New', $fixture[0]->getContent());
        self::assertSame('Something New', $fixture[0]->getCreated_at());
        self::assertSame('Something New', $fixture[0]->getRead());
        self::assertSame('Something New', $fixture[0]->getReceiver());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Notification();
        $fixture->setObject('My Title');
        $fixture->setContent('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setRead('My Title');
        $fixture->setReceiver('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/notification/');
    }
}

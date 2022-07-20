<?php

namespace App\Test\Controller;

use App\Entity\Messages;
use App\Repository\MessagesRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessagesControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private MessagesRepository $repository;
    private string $path = '/messages/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Messages::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Message index');

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
            'message[created_at]' => 'Testing',
            'message[content]' => 'Testing',
            'message[is_read]' => 'Testing',
            'message[author]' => 'Testing',
            'message[receiver]' => 'Testing',
        ]);

        self::assertResponseRedirects('/messages/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Messages();
        $fixture->setCreated_at('My Title');
        $fixture->setContent('My Title');
        $fixture->setIs_read('My Title');
        $fixture->setAuthor('My Title');
        $fixture->setReceiver('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Message');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Messages();
        $fixture->setCreated_at('My Title');
        $fixture->setContent('My Title');
        $fixture->setIs_read('My Title');
        $fixture->setAuthor('My Title');
        $fixture->setReceiver('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'message[created_at]' => 'Something New',
            'message[content]' => 'Something New',
            'message[is_read]' => 'Something New',
            'message[author]' => 'Something New',
            'message[receiver]' => 'Something New',
        ]);

        self::assertResponseRedirects('/messages/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCreated_at());
        self::assertSame('Something New', $fixture[0]->getContent());
        self::assertSame('Something New', $fixture[0]->getIs_read());
        self::assertSame('Something New', $fixture[0]->getAuthor());
        self::assertSame('Something New', $fixture[0]->getReceiver());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Messages();
        $fixture->setCreated_at('My Title');
        $fixture->setContent('My Title');
        $fixture->setIs_read('My Title');
        $fixture->setAuthor('My Title');
        $fixture->setReceiver('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/messages/');
    }
}

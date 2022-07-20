<?php

namespace App\Test\Controller;

use App\Entity\Mail;
use App\Repository\MailRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MailControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private MailRepository $repository;
    private string $path = '/mail/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Mail::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Mail index');

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
            'mail[object]' => 'Testing',
            'mail[content]' => 'Testing',
            'mail[sent_date]' => 'Testing',
            'mail[receiver]' => 'Testing',
        ]);

        self::assertResponseRedirects('/mail/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Mail();
        $fixture->setObject('My Title');
        $fixture->setContent('My Title');
        $fixture->setSent_date('My Title');
        $fixture->setReceiver('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Mail');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Mail();
        $fixture->setObject('My Title');
        $fixture->setContent('My Title');
        $fixture->setSent_date('My Title');
        $fixture->setReceiver('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'mail[object]' => 'Something New',
            'mail[content]' => 'Something New',
            'mail[sent_date]' => 'Something New',
            'mail[receiver]' => 'Something New',
        ]);

        self::assertResponseRedirects('/mail/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getObject());
        self::assertSame('Something New', $fixture[0]->getContent());
        self::assertSame('Something New', $fixture[0]->getSent_date());
        self::assertSame('Something New', $fixture[0]->getReceiver());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Mail();
        $fixture->setObject('My Title');
        $fixture->setContent('My Title');
        $fixture->setSent_date('My Title');
        $fixture->setReceiver('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/mail/');
    }
}

<?php

namespace App\Test\Controller;

use App\Entity\Opinion;
use App\Repository\OpinionRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OpinionControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private OpinionRepository $repository;
    private string $path = '/opinion/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Opinion::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Opinion index');

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
            'opinion[notation]' => 'Testing',
            'opinion[message]' => 'Testing',
            'opinion[created_at]' => 'Testing',
            'opinion[emitter]' => 'Testing',
            'opinion[receptor]' => 'Testing',
        ]);

        self::assertResponseRedirects('/opinion/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Opinion();
        $fixture->setNotation('My Title');
        $fixture->setMessage('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setEmitter('My Title');
        $fixture->setReceptor('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Opinion');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Opinion();
        $fixture->setNotation('My Title');
        $fixture->setMessage('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setEmitter('My Title');
        $fixture->setReceptor('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'opinion[notation]' => 'Something New',
            'opinion[message]' => 'Something New',
            'opinion[created_at]' => 'Something New',
            'opinion[emitter]' => 'Something New',
            'opinion[receptor]' => 'Something New',
        ]);

        self::assertResponseRedirects('/opinion/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getNotation());
        self::assertSame('Something New', $fixture[0]->getMessage());
        self::assertSame('Something New', $fixture[0]->getCreated_at());
        self::assertSame('Something New', $fixture[0]->getEmitter());
        self::assertSame('Something New', $fixture[0]->getReceptor());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Opinion();
        $fixture->setNotation('My Title');
        $fixture->setMessage('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setEmitter('My Title');
        $fixture->setReceptor('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/opinion/');
    }
}

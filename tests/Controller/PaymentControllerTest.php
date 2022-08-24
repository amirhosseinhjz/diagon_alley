<?php

namespace App\Test\Controller\Payment;

use App\Entity\Payment\Payment;
use App\Repository\Payment\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PaymentControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private PaymentRepository $repository;
    private string $path = '/payment/payment/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Payment::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Payment index');

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
            'payment[type]' => 'Testing',
            'payment[paidAmount]' => 'Testing',
            'payment[createdAt]' => 'Testing',
            'payment[status]' => 'Testing',
            'payment[code]' => 'Testing',
        ]);

        self::assertResponseRedirects('/payment/payment/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Payment();
        $fixture->setType('My Title');
        $fixture->setPaidAmount('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setStatus('My Title');
        $fixture->setCode('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Payment');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Payment();
        $fixture->setType('My Title');
        $fixture->setPaidAmount('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setStatus('My Title');
        $fixture->setCode('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'payment[type]' => 'Something New',
            'payment[paidAmount]' => 'Something New',
            'payment[createdAt]' => 'Something New',
            'payment[status]' => 'Something New',
            'payment[code]' => 'Something New',
        ]);

        self::assertResponseRedirects('/payment/payment/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getPaidAmount());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getCode());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Payment();
        $fixture->setType('My Title');
        $fixture->setPaidAmount('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setStatus('My Title');
        $fixture->setCode('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/payment/payment/');
    }
}

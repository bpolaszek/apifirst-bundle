<?php
use BenTools\ApiFirstBundle\TestSuite\Model\Country;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class APITest extends WebTestCase {

    public static function setUpBeforeClass() {

        parent::setUpBeforeClass();

        $kernel = static::createKernel();
        $kernel->boot();
        $em         = $kernel->getContainer()->get('doctrine')->getManager();
        $schemaTool = new SchemaTool($em);
        $metadata   = $em->getMetadataFactory()->getAllMetadata();

        // Drop and recreate tables for all entities
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function testCreateCountry() {

        $client = static::createClient();
        $client->request('GET', '/countries', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertStatusCode(200, $client);
        $this->assertJson($client->getResponse()->getContent());
        $json = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals([], $json);

        $client->request('POST', '/countries', [
            'name' => 'fr',
        ], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        // We should get a validation error for Assert\Minlength(3).
        $this->assertStatusCode(400, $client);
        $this->assertValidationErrors(['data.name'], $client->getContainer());

        $client->request('POST', '/countries', [
            'name' => 'france',
        ], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertStatusCode(201, $client);
        $redirect = $client->getResponse()->headers->get('Location');
        $this->assertSame('/countries/1', $redirect);

        $crawler = $client->request('GET', $redirect, [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertStatusCode(200, $client);
        $this->assertJson($client->getResponse()->getContent());
        $json = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals([
            'id'   => 1,
            'name' => 'france',
        ], $json);

        $country = $this->getContainer()->get('doctrine')->getRepository(Country::class)->find(1);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertSame('france', $country->getName());

        return $country;

    }

    /**
     * @param $country
     * @depends testCreateCountry
     */
    public function testEditCountry($country) {
        $this->assertInstanceOf(Country::class, $country);

        $client = static::makeClient();

        $client->request('PATCH', '/countries/1', [
            'name' => 'fr',
        ], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        // We should get a validation error for Assert\Minlength(3).
        $this->assertStatusCode(400, $client);
        $this->assertValidationErrors(['data.name'], $client->getContainer());

        $client->request('PATCH', '/countries/1', [
            'name' => 'France',
        ], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertStatusCode(204, $client);
        $redirect = $client->getResponse()->headers->get('Location');
        $this->assertSame('/countries/1', $redirect);

        $country = $this->getContainer()->get('doctrine')->getRepository(Country::class)->find(1);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertSame('France', $country->getName());

        return $country;
    }

    /**
     * @param $country
     * @depends testEditCountry
     */
    public function testDeleteAction($country) {
        $this->assertInstanceOf(Country::class, $country);

        $client = static::makeClient();

        $client->request('DELETE', '/countries/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertStatusCode(204, $client);

    }

}
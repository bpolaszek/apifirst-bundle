<?php
use BenTools\ApiFirstBundle\TestSuite\Model\Country;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class UITest extends WebTestCase {

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

        $client  = static::makeClient();
        $crawler = $client->request('GET', '/countries');
        $this->assertStatusCode(200, $client);
        $this->assertCount(0, $crawler->filter('ul.countries > li'));

        $crawler = $client->request('GET', '/countries/create');
        $this->assertStatusCode(200, $client);
        $this->assertCount(1, $crawler->filter('form'));
        $this->assertCount(1, $crawler->filter('input#country_name'));
        $this->assertCount(1, $crawler->filter('input#country__token'));

        $form = $crawler->selectButton('submit')->form();

        $client->submit($form);

        // We should get a validation error for the empty fields.
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.name'], $client->getContainer());

        $client->submit($form, [
            'country[name]' => 'fr',
        ]);

        // We should get a validation error for Assert\Minlength(3).
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.name'], $client->getContainer());

        $client->submit($form, [
            'country[name]' => 'france',
        ]);

        $this->assertStatusCode(302, $client);
        $redirect = $client->getResponse()->headers->get('Location');
        $this->assertSame('/countries/1', $redirect);

        $crawler = $client->request('GET', '/countries');
        $this->assertStatusCode(200, $client);
        $this->assertCount(1, $crawler->filter('ul.countries > li'));

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

        $client  = static::makeClient();
        $crawler = $client->request('GET', '/countries/1/edit');
        $this->assertStatusCode(200, $client);
        $this->assertCount(1, $crawler->filter('form'));
        $this->assertCount(1, $crawler->filter('input#country_name'));
        $this->assertCount(1, $crawler->filter('input#country__token'));

        $form = $crawler->selectButton('submit')->form();
        $client->submit($form, [
            'country[name]' => 'fr',
        ]);

        // We should get a validation error for Assert\Minlength(3).
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.name'], $client->getContainer());

        $client->submit($form, [
            'country[name]' => 'France',
        ]);

        $this->assertStatusCode(302, $client);
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

        $client  = static::makeClient();
        $crawler = $client->request('GET', '/countries');
        $this->assertStatusCode(200, $client);
        $this->assertCount(1, $crawler->filter('ul.countries > li'));
        $this->assertCount(1, $crawler->filter('input#country__token'));

        $form = $crawler->selectButton('delete')->form();
        $client->submit($form);

    }

}
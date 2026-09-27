<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PeselControllerTest extends WebTestCase
{
    public function testFormWithValidPesel(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $client->submitForm('Sprawdź', ['pesel[pesel]' => '44051401359']);

        $this->assertSelectorTextContains('.alert-success', 'Numer PESEL jest poprawny.');
        $this->assertSelectorTextContains('.alert-success', '1944-05-14');
    }

    public function testFormWithInvalidPesel(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $client->submitForm('Sprawdź', ['pesel[pesel]' => '44051401358']);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorNotExists('.alert-success');
    }

    public function testApiValidPesel(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/pesel', [], [], ['CONTENT_TYPE' => 'application/json'], '{"pesel": "44051401359"}');

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($response['valid']);
        $this->assertSame('1944-05-14', $response['birthDate']);
        $this->assertSame('mężczyzna', $response['gender']);
    }

    public function testApiInvalidPesel(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/pesel', [], [], ['CONTENT_TYPE' => 'application/json'], '{"pesel": "44051401358"}');

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse($response['valid']);
        $this->assertCount(1, $response['errors']);
    }

    public function testApiWithoutPesel(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/pesel', [], [], ['CONTENT_TYPE' => 'application/json'], '{}');

        $this->assertResponseStatusCodeSame(400);
    }
}

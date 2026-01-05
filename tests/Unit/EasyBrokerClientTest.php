<?php

namespace EasyBroker\Tests\Unit;

use EasyBroker\Client\EasyBrokerClient;
use EasyBroker\Property\Property;
use EasyBroker\Exception\EasyBrokerException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\TestCase;

/**
 * Clase de prueba para EasyBrokerClient
 */
class EasyBrokerClientTest extends TestCase
{
    /**
     * @var EasyBrokerClient Cliente de prueba
     */
    private $client;
    
    /**
     * @var MockHandler Mock handler para Guzzle
     */
    private $mockHandler;
    
    /**
     * Configuracion inicial para cada prueba
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear mock handler para Guzzle
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);
        
        // Crear cliente con HTTP client mockeado
        $this->client = new EasyBrokerClient('test-api-key', 'https://api.test.com/v1', $httpClient);
    }
    
    /**
     * Prueba que el cliente se crea correctamente
     */
    public function testClientCreation()
    {
        $this->assertInstanceOf(EasyBrokerClient::class, $this->client);
    }
    
    /**
     * Prueba la obtencion exitosa de propiedades
     */
    public function testGetAllPropertiesSuccess()
    {
        // Mock response data
        $mockData = [
            'pagination' => [
                'limit' => 3,
                'page' => 1,
                'total' => 10,
                'next_page' => 'https://api.test.com/v1/properties?page=2'
            ],
            'content' => [
                [
                    'public_id' => 'EB-TEST-1',
                    'title' => 'Test Property 1',
                    'location' => 'Test Location 1',
                    'property_type' => 'Casa'
                ],
                [
                    'public_id' => 'EB-TEST-2',
                    'title' => 'Test Property 2',
                    'location' => 'Test Location 2',
                    'property_type' => 'Departamento'
                ]
            ]
        ];
        
        // Configurar mock response
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], json_encode($mockData))
        );
        
        // Ejecutar metodo bajo prueba
        $properties = $this->client->getAllProperties(1, 3);
        
        // Verificar resultados
        $this->assertIsArray($properties);
        $this->assertCount(2, $properties);
        $this->assertInstanceOf(Property::class, $properties[0]);
        $this->assertEquals('Test Property 1', $properties[0]->getTitle());
        $this->assertEquals('Test Property 2', $properties[1]->getTitle());
        
        // Verificar informacion de paginacion
        $pagination = $this->client->getPaginationInfo();
        $this->assertNotNull($pagination);
        $this->assertEquals(1, $pagination['page']);
        $this->assertEquals(10, $pagination['total']);
    }
    
    /**
     * Prueba la obtencion de titulos de propiedades
     */
    public function testGetAllPropertyTitles()
    {
        // Mock response data
        $mockData = [
            'pagination' => ['limit' => 2, 'page' => 1, 'total' => 5],
            'content' => [
                ['public_id' => 'EB-1', 'title' => 'Title 1', 'location' => 'Loc 1', 'property_type' => 'Casa'],
                ['public_id' => 'EB-2', 'title' => 'Title 2', 'location' => 'Loc 2', 'property_type' => 'Depto']
            ]
        ];
        
        // Configurar mock response
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], json_encode($mockData))
        );
        
        // Ejecutar metodo bajo prueba
        $titles = $this->client->getAllPropertyTitles();
        
        // Verificar resultados
        $this->assertIsArray($titles);
        $this->assertEquals(['Title 1', 'Title 2'], $titles);
    }
    
    /**
     * Prueba el manejo de errores HTTP
     */
    public function testHttpErrorHandling()
    {
        // Configurar mock response con error
        $this->mockHandler->append(
            new Response(404, [], 'Not Found')
        );
        
        // Verificar que se lanza la excepcion
        $this->expectException(EasyBrokerException::class);
        $this->expectExceptionCode(404);
        
        $this->client->getAllProperties();
    }
    
    /**
     * Prueba el manejo de errores de red
     */
    public function testNetworkErrorHandling()
    {
        // Configurar mock response con error de red
        $this->mockHandler->append(
            new RequestException('Connection failed', new Request('GET', 'test'))
        );
        
        // Verificar que se lanza la excepcion
        $this->expectException(EasyBrokerException::class);
        
        $this->client->getAllProperties();
    }
    
    /**
     * Prueba el manejo de JSON invalido
     */
    public function testInvalidJsonResponse()
    {
        // Configurar mock response con JSON invalido
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], 'Invalid JSON {')
        );
        
        // Verificar que se lanza la excepcion
        $this->expectException(EasyBrokerException::class);
        
        $this->client->getAllProperties();
    }
    
    /**
     * Prueba con respuesta vacia
     */
    public function testEmptyResponse()
    {
        // Mock response data vacia
        $mockData = [
            'pagination' => ['limit' => 20, 'page' => 1, 'total' => 0],
            'content' => []
        ];
        
        // Configurar mock response
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], json_encode($mockData))
        );
        
        // Ejecutar metodo bajo prueba
        $properties = $this->client->getAllProperties();
        
        // Verificar resultados
        $this->assertIsArray($properties);
        $this->assertEmpty($properties);
    }
    
    /**
     * Prueba el constructor con diferentes configuraciones
     */
    public function testConstructorConfigurations()
    {
        // Test con URL personalizada
        $client1 = new EasyBrokerClient('test-key', 'https://custom.api.com/v2');
        $this->assertInstanceOf(EasyBrokerClient::class, $client1);
        
        // Test sin especificar URL (usa default)
        $client2 = new EasyBrokerClient('test-key');
        $this->assertInstanceOf(EasyBrokerClient::class, $client2);
    }
    
    /**
     * Prueba la clase Property
     */
    public function testPropertyClass()
    {
        $data = [
            'public_id' => 'TEST-123',
            'title' => 'Beautiful House',
            'location' => 'Nice Neighborhood',
            'property_type' => 'House'
        ];
        
        $property = new Property($data);
        
        $this->assertEquals('TEST-123', $property->getPublicId());
        $this->assertEquals('Beautiful House', $property->getTitle());
        $this->assertEquals('Nice Neighborhood', $property->getLocation());
        $this->assertEquals('House', $property->getPropertyType());
        
        // Test toArray
        $array = $property->toArray();
        $this->assertEquals($data, $array);
        
        // Test toJson
        $json = $property->toJson();
        $decoded = json_decode($json, true);
        $this->assertEquals($data, $decoded);
    }
}

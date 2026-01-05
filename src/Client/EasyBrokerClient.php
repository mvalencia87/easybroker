<?php

namespace EasyBroker\Client;

use EasyBroker\Property\Property;
use EasyBroker\Exception\EasyBrokerException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Cliente para consumir la API de EasyBroker
 * Implementa la interfaz EasyBrokerClientInterface
 */
class EasyBrokerClient implements EasyBrokerClientInterface
{
	/**
	 * @var Client Cliente HTTP Guzzle
	 */
	private Client $httpClient;

	/**
	 * @var string URL base de la API
	 */
	private string $apiBaseUrl;

	/**
	 * @var string Clave de autorizacion de la API
	 */
	private string $apiKey;

	/**
	 * @var array|null Informacion de paginacion de la ultima consulta
	 */
	private ?array $paginationInfo = null;

	/**
	 * Constructor del cliente
	 *
	 * @param string $apiKey Clave de la API
	 * @param string $baseUrl URL base de la API (opcional)
	 * @param Client|null $httpClient Cliente HTTP personalizado (opcional)
	 */
	public function __construct(
	    string $apiKey,
	    string $baseUrl = 'https://api.stagingeb.com/v1',
	    ?Client $httpClient = null
	) {
	    $this->apiKey = $apiKey;
	    $this->apiBaseUrl = rtrim($baseUrl, '/');

	    // Usar cliente proporcionado o crear uno nuevo
	    $this->httpClient = $httpClient ?? new Client([
	        'timeout' => 30,
	        'connect_timeout' => 10,
	        'headers' => [
	            'Accept' => 'application/json',
	            'User-Agent' => 'EasyBrokerPHPClient/1.0'
	        ]
	    ]);
	}

	/**
	 * Realiza una peticion GET a la API
	 *
	 * @param string $endpoint Endpoint de la API
	 * @param array $queryParams Parametros de consulta
	 * @return array Respuesta decodificada de la API
	 * @throws EasyBrokerException
	 */
	private function makeRequest(string $endpoint, array $queryParams = []): array
	{
	    $url = $this->apiBaseUrl . $endpoint;

	    try {
	        $response = $this->httpClient->request('GET', $url, [
	            'query' => $queryParams,
	            'headers' => [
	                'X-Authorization' => $this->apiKey
	            ]
	        ]);

	        $statusCode = $response->getStatusCode();
	        $body = $response->getBody()->getContents();

	        if ($statusCode !== 200) {
	            throw new EasyBrokerException(
	                "Error en la API: Codigo HTTP $statusCode",
	                $statusCode
	            );
	        }

	        $data = json_decode($body, true);

	        if (json_last_error() !== JSON_ERROR_NONE) {
	            throw new EasyBrokerException(
	                'Error al decodificar la respuesta JSON: ' . json_last_error_msg(),
	                500
	            );
	        }

	        return $data;

	    } catch (RequestException $e) {
	        $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
	        throw new EasyBrokerException(
	            "Error en la peticion HTTP: " . $e->getMessage(),
	            $statusCode,
	            $e
	        );
	    } catch (GuzzleException $e) {
	        throw new EasyBrokerException(
	            "Error de Guzzle: " . $e->getMessage(),
	            0,
	            $e
	        );
	    }
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAllProperties(int $page = 1, int $limit = 20): array
	{
	    $data = $this->makeRequest('/properties', [
	        'page' => $page,
	        'limit' => $limit
	    ]);

	    // Guardar informacion de paginacion
	    $this->paginationInfo = $data['pagination'] ?? null;

	    // Convertir datos a objetos Property
	    $properties = [];
	    foreach ($data['content'] as $propertyData) {
	        $properties[] = new Property($propertyData);
	    }

	    return $properties;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAllPropertyTitles(): array
	{
	    $properties = $this->getAllProperties();

	    $titles = [];
	    foreach ($properties as $property) {
	        $titles[] = $property->getTitle();
	    }

	    return $titles;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPaginationInfo(): ?array
	{
	    return $this->paginationInfo;
	}

	/**
	 * Obtiene todas las propiedades de todas las paginas disponibles
	 *
	 * Nota: Este metodo puede consumir muchos recursos si hay muchas propiedades
	 *
	 * @param int $limitPerPage Limite de propiedades por pagina
	 * @return array Lista de todas las propiedades
	 * @throws EasyBrokerException
	 */
	public function getAllPropertiesFromAllPages(int $limitPerPage = 20): array
	{
	    $allProperties = [];
	    $currentPage = 1;
	    $hasMorePages = true;

	    while ($hasMorePages) {
	        $data = $this->makeRequest('/properties', [
	            'page' => $currentPage,
	            'limit' => $limitPerPage
	        ]);

	        // Procesar propiedades de la pagina actual
	        foreach ($data['content'] as $propertyData) {
	            $allProperties[] = new Property($propertyData);
	        }

	        // Verificar si hay mas paginas
	        $pagination = $data['pagination'] ?? [];
	        $hasMorePages = isset($pagination['next_page']) && !empty($pagination['next_page']);
	        $currentPage++;

	        // Pequenia pausa para no sobrecargar la API
	        if ($hasMorePages) {
	            usleep(100000); // 100ms
	        }
	    }

	    return $allProperties;
	}

	/**
	 * Obtiene los titulos de todas las propiedades de todas las paginas
	 *
	 * @return array Lista de todos los titulos
	 * @throws EasyBrokerException
	 */
	public function getAllPropertyTitlesFromAllPages(): array
	{
	    $allProperties = $this->getAllPropertiesFromAllPages();

	    $titles = [];
	    foreach ($allProperties as $property) {
	        $titles[] = $property->getTitle();
	    }

	    return $titles;
	}
}

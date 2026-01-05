<?php

namespace EasyBroker\Client;

use EasyBroker\Property\Property;

/**
 * Interfaz para el cliente de EasyBroker
 * Define los metodos que debe implementar cualquier cliente de la API
 */
interface EasyBrokerClientInterface
{
	/**
	 * Obtiene todas las propiedades disponibles
	 *
	 * @param int $page Pagina a obtener
	 * @param int $limit Limite de propiedades por pagina
	 * @return array Lista de objetos Property
	 * @throws \EasyBroker\Exception\EasyBrokerException
	 */
	public function getAllProperties(int $page = 1, int $limit = 20): array;

	/**
	 * Obtiene los titulos de todas las propiedades
	 *
	 * @return array Lista de titulos de propiedades
	 * @throws \EasyBroker\Exception\EasyBrokerException
	 */
	public function getAllPropertyTitles(): array;

	/**
	 * Obtiene informacion de paginacion de la ultima consulta
	 *
	 * @return array|null Informacion de paginacion o null si no hay
	 */
	public function getPaginationInfo(): ?array;
}

<?php

namespace EasyBroker\Property;

/**
 * Clase que representa una propiedad inmobiliaria
 */
class Property
{
	/**
	 * @var string ID publico de la propiedad
	 */
	private string $publicId;

	/**
	 * @var string Titulo de la propiedad
	 */
	private string $title;

	/**
	 * @var string Ubicacion de la propiedad
	 */
	private string $location;

	/**
	 * @var string Tipo de propiedad (Casa, Departamento, Oficina, etc.)
	 */
	private string $propertyType;

	/**
	 * Constructor de la clase Property
	 *
	 * @param array $data Datos de la propiedad
	 */
	public function __construct(array $data)
	{
		$this->publicId = $data['public_id'] ?? '';
		$this->title = $data['title'] ?? '';
		$this->location = $data['location'] ?? '';
		$this->propertyType = $data['property_type'] ?? '';
	}

	/**
	 * Obtiene el ID publico de la propiedad
	 *
	 * @return string
	 */
	public function getPublicId(): string
	{
		return $this->publicId;
	}

	/**
	 * Obtiene el titulo de la propiedad
	 *
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * Obtiene la ubicacion de la propiedad
	 *
	 * @return string
	 */
	public function getLocation(): string
	{
		return $this->location;
	}

	/**
	 * Obtiene el tipo de propiedad
	 *
	 * @return string
	 */
	public function getPropertyType(): string
	{
		return $this->propertyType;
	}

	/**
	 * Convierte la propiedad a un array asociativo
	 *
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'public_id' => $this->publicId,
			'title' => $this->title,
			'location' => $this->location,
			'property_type' => $this->propertyType
		];
	}

	/**
	 * Convierte la propiedad a formato JSON
	 *
	 * @return string
	 */
	public function toJson(): string
	{
		return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}
}

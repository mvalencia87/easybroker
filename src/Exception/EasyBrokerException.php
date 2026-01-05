<?php

namespace EasyBroker\Exception;

/**
 * Excepcion personalizada para errores relacionados con la API de EasyBroker
 */
class EasyBrokerException extends \Exception
{
	/**
	 * Constructor de la excepcion
	 *
	 * @param string $message Mensaje de error
	 * @param int $code Codigo de error HTTP
	 * @param \Throwable|null $previous Excepcion previa
	 */
	public function __construct(string $message, int $code = 0, \Throwable $previous = null)
	{
	    parent::__construct($message, $code, $previous);
	}

	/**
	 * Representacion en string de la excepcion
	 *
	 * @return string
	 */
	public function __toString(): string
	{
	    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}

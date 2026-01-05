# Cliente EasyBroker PHP

Cliente PHP para consumir la API de EasyBroker. Este paquete permite obtener propiedades inmobiliarias desde la API de EasyBroker.

## Caracteristicas

- Obtener propiedades con paginacion
- Obtener todos los titulos de propiedades
- Manejo robusto de errores
- Pruebas unitarias completas
- Interfaz fluida y facil de usar
- Compatible con PHP 8.0+

## Instalacion

### Requisitos Previos

- PHP 8.0 o superior
- Composer
- Extension cURL de PHP
- Extension JSON de PHP

### Instalacion con Composer

```
# Clonar el repositorio
git clone https://github.com/mvalencia87/easybroker.git
cd easybroker

# Instalar dependencias
composer install
```

### Ejecucion

- En un navegador web: IP/index.php
- En terminal: php cli.php [opciones]
```
php cli.php --limit=34 --page=1
php cli.php --help
php cli.php --all
php cli.php --limit=32
php cli.php --page=23
```

### Test
```
composer test
```


### Estructura del proyecto
```
easybroker-client/
│
├── src/
│   ├── Client/
│   │   ├── EasyBrokerClient.php
│   │   └── EasyBrokerClientInterface.php
│   ├── Property/
│   │   └── Property.php
│   └── Exception/
│       └── EasyBrokerException.php
│
├── tests/
│   ├── Unit/
│   │   └── EasyBrokerClientTest.php
│   └── bootstrap.php
│
├── index.php
├── cli.php
├── composer.json
└── README.md
```
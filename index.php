<?php

require_once __DIR__ . '/vendor/autoload.php';

use EasyBroker\Client\EasyBrokerClient;
use EasyBroker\Exception\EasyBrokerException;

// Configuracion
$apiKey = 'l7u502p8v46ba3ppgvj5y2aad50lb9';
$limit = 31; // Numero de propiedades a mostrar
$page = 1; // Pagina a mostrar

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propiedades de EasyBroker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            padding: 20px;
        }

        h1 {
            text-align: center;
            padding-bottom: 10px;
        }

        .properties-list {
            list-style: none;
        }

        .property-item {
            padding: 20px;
            margin-bottom: 15px;
        }

        .property-title {
            font-weight: 600;
        }

        .property-details {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .property-type {
            background: #e3f2fd;
            color: #1565c0;
            padding: 2px 8px;
            font-size: 0.8em;
        }

        .error {
            text-align: center;
            background: #ffebee;
            color: #c62828;
            padding: 15px;
        }

        .success {
            text-align: center;
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
        }

        .pagination-info {
            text-align: center;
            background: #fff3e0;
            color: #ef6c00;
            padding: 10px 15px;
        }
    </style>
</head>
<body>
    <div>
        <h1>Propiedades de EasyBroker</h1>
        <hr/>

        <?php
        try {
            // Crear cliente
            $client = new EasyBrokerClient($apiKey);
            
            // Obtener propiedades
            $properties = $client->getAllProperties($page, $limit);
            
            // Obtener informacion de paginacion
            $pagination = $client->getPaginationInfo();
            
            echo '<div class="success">
                    <strong>Endpoint:</strong> https://api.stagingeb.com/v1/properties<br>
                    <strong>Mostrando:</strong> ' . count($properties) . ' propiedades<br>
                    <strong>Tiempo:</strong> ' . date("Y-m-d H:i:s") . '
                </div><hr/>';
            
            if (!empty($properties)) {
                echo '<ul class="properties-list">';
                foreach ($properties as $property) {
                    echo '<li class="property-item">
                            <div class="property-title">' . htmlspecialchars($property->getTitle()) . '</div>
                            <div class="property-details">
                            <span><strong>ID:</strong> ' . htmlspecialchars($property->getPublicId()) . '</span>
                            <span><strong>Ubicacion:</strong> ' . htmlspecialchars($property->getLocation()) . '</span>
                            <span class="property-type">' . htmlspecialchars($property->getPropertyType()) . '</span>
                            </div>
                        </li>';
                }
                echo '</ul>';
                
                if ($pagination) {
                    echo '<div class="pagination-info">
                            <strong>Paginacion:</strong> Pagina ' . $pagination['page'] . ' de ' . ceil($pagination['total'] / $pagination['limit']) . ' | Total de propiedades: ' . $pagination['total'] . '
                        </div>';
                }
            } else {
                echo '<div class="error">No se encontraron propiedades.</div>';
            }
            
        } catch (EasyBrokerException $e) {
            echo '<div class="error">
                    <strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '
                    <br><small>Codigo: ' . $e->getCode() . '</small>
                </div>';
        } catch (Exception $e) {
            echo '<div class="error">
                    <strong>Error inesperado:</strong> ' . htmlspecialchars($e->getMessage()) . '
                </div>';
        }
        ?>
    </div>
</body>
</html>

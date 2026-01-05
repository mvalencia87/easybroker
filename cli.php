#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use EasyBroker\Client\EasyBrokerClient;
use EasyBroker\Exception\EasyBrokerException;

/**
 * Script de linea de comandos para obtener propiedades de EasyBroker
 * 
 * Uso: php cli.php [--limit=LIMIT] [--page=PAGE] [--all]
 */

// Configuracion por defecto
$apiKey = 'l7u502p8v46ba3ppgvj5y2aad50lb9';
$limit = 20;
$page = 4;
$showAll = false;

// Parsear argumentos de linea de comandos
$options = getopt('', ['limit:', 'page:', 'all', 'help']);
if (isset($options['help'])) {
    echo "Uso: php cli.php [opciones]\n\n";
    echo "Opciones:\n";
    echo "  --limit=NUM     Limite de propiedades por pagina (default: 10)\n";
    echo "  --page=NUM      Numero de pagina (default: 1)\n";
    echo "  --all           Obtener todas las propiedades de todas las paginas\n";
    echo "  --help          Mostrar esta ayuda\n";
    exit(0);
}

if (isset($options['limit'])) {
    $limit = (int)$options['limit'];
}

if (isset($options['page'])) {
    $page = (int)$options['page'];
}

if (isset($options['all'])) {
    $showAll = true;
}

echo "========================================\n";
echo "     Propiedades de EasyBroker CLI      \n";
echo "========================================\n\n";

try {
    // Crear cliente
    $client = new EasyBrokerClient($apiKey);
    
    if ($showAll) {
        echo "Obteniendo TODAS las propiedades...\n";
        $titles = $client->getAllPropertyTitlesFromAllPages();
        echo "Se encontraron " . count($titles) . " propiedades en total\n\n";
    } else {
        echo "Obteniendo propiedades (Pagina: $page, Limite: $limit)...\n";
        $properties = $client->getAllProperties($page, $limit);
        $titles = array_map(fn($p) => $p->getTitle(), $properties);
        
        $pagination = $client->getPaginationInfo();
        if ($pagination) {
            echo "Paginacion: Pagina {$pagination['page']} de " .
                 ceil($pagination['total'] / $pagination['limit']) . 
                 " | Total: {$pagination['total']} propiedades\n\n";
        }
    }
    
    // Mostrar titulos
    if (!empty($titles)) {
        echo "Lista de Titulos:\n";
        echo "====================\n";
        foreach ($titles as $index => $title) {
            $num = $index + 1;
            echo "$num. $title\n";
        }
    } else {
        echo "No se encontraron propiedades.\n";
    }
    
} catch (EasyBrokerException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "   Codigo: " . $e->getCode() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Error inesperado: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nProceso completado exitosamente.\n";

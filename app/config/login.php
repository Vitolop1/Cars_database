<?php
/**
 * Database Connection Configuration
 * 
 * Este archivo configura la conexión PDO a MariaDB usando
 * las credenciales definidas en docker-compose.yml
 */

// Configuración de la base de datos
$host = "db";  // Nombre del servicio en docker-compose
$db   = "cars_db";
$user = "cars_user";
$pass = "cars_pass";
$charset = "utf8mb4";

// DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opciones de PDO para mejor manejo de errores y seguridad
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Intentar conexión a la base de datos
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // En caso de error, mostrar mensaje y terminar la ejecución
    http_response_code(500);
    
    // Mensaje amigable para el usuario
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Database Error</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                padding: 20px;
            }
            .error-container {
                background: white;
                border-radius: 12px;
                padding: 40px;
                max-width: 600px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            }
            h1 {
                color: #dc3545;
                margin: 0 0 20px 0;
            }
            .error-details {
                background: #f8d7da;
                border-left: 4px solid #dc3545;
                padding: 15px;
                border-radius: 4px;
                margin: 20px 0;
            }
            pre {
                background: #f4f4f4;
                padding: 15px;
                border-radius: 4px;
                overflow-x: auto;
                font-size: 14px;
            }
            .troubleshooting {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                padding: 20px;
                border-radius: 4px;
                margin-top: 20px;
            }
            .troubleshooting h2 {
                margin: 0 0 15px 0;
                font-size: 18px;
            }
            ul {
                margin: 10px 0 0 20px;
            }
            li {
                margin: 8px 0;
            }
            code {
                background: #f4f4f4;
                padding: 2px 6px;
                border-radius: 3px;
                font-family: 'Courier New', monospace;
            }
        </style>
    </head>
    <body>
        <div class='error-container'>
            <h1>🚫 Database Connection Error</h1>
            <p>Could not connect to the database. Please check your configuration.</p>
            
            <div class='error-details'>
                <strong>Connection Details:</strong><br>
                Host: <code>{$host}</code><br>
                Database: <code>{$db}</code><br>
                User: <code>{$user}</code>
            </div>
            
            <pre>" . htmlspecialchars($e->getMessage()) . "</pre>
            
            <div class='troubleshooting'>
                <h2>🔧 Troubleshooting Steps</h2>
                <ul>
                    <li>Check if the database container is running: <code>docker compose ps</code></li>
                    <li>View database logs: <code>docker compose logs db</code></li>
                    <li>Ensure the database is fully initialized (this may take 10-20 seconds)</li>
                    <li>If the error persists, remove old volumes: <code>docker compose down -v</code></li>
                    <li>Rebuild containers: <code>docker compose up -d --build</code></li>
                </ul>
            </div>
        </div>
    </body>
    </html>";
    
    exit;
}

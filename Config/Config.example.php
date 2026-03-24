<?php
/**
 * Configuración de la aplicación.
 * Copiar a Config.php y ajustar valores, o usar variables de entorno (Docker).
 *
 * Datos de referencia (entorno local/producción):
 * - host: localhost:3306
 * - db: zgroupztrack
 * - user: ztrack2023
 * - APIs: 161.132.206.104 (8000, 9010, 9020, 9050), url_nueva: 161.132.53.51:9050
 */
$useEnv = getenv('DB_HOST') !== false;

if ($useEnv) {
    define('host', getenv('DB_HOST') ?: 'mysql');
    define('port', getenv('DB_PORT') ?: '3306');
    define('db', getenv('DB_NAME') ?: 'zgroupot');
    define('user', getenv('DB_USER') ?: 'cerroprieto');
    define('pass', getenv('DB_PASS') ?: 'cerroprieto');
    define('charset', getenv('DB_CHARSET') ?: 'utf8mb4');
    define('base_url', rtrim(getenv('BASE_URL') ?: 'http://localhost:8080/', '/') . '/');
    define('urlapi', getenv('URLAPI') ?: '');
    define('urlapiMysql', getenv('URLAPI_MYSQL') ?: '');
    define('urlapiMongo', getenv('URLAPI_MONGO') ?: '');
    define('urlapiMongo2', getenv('URLAPI_MONGO2') ?: '');
    define('url_nueva', getenv('URL_NUEVA') ?: '');
} else {
    define('host', 'localhost');
    define('port', '3306');
    define('db', 'zgroupztrack');
    define('user', 'ztrack2023');
    define('pass', 'lpmp2018');
    define('charset', 'utf8');
    define('base_url', '/cerroprieto/');
    define('urlapi', 'http://161.132.206.104:8000');
    define('urlapiMysql', 'http://161.132.206.104:9010');
    define('urlapiMongo', 'http://161.132.206.104:9020');
    define('urlapiMongo2', 'http://161.132.206.104:9050');
    define('url_nueva', 'http://161.132.53.51:9050');
}

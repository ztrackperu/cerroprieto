<?php
/**
 * Configuración unificada: producción (sin Docker) y Docker / pruebas.
 *
 * Cómo elige el modo:
 * - Si existe DB_HOST (tras cargar .env, ver abajo), se usan constantes desde getenv().
 * - Si DB_HOST está vacío o no existe, se usa el bloque else (valores fijos en este archivo).
 *
 * Archivo .env (raíz del proyecto): opcional. Se lee si existe; las variables que ya
 * vienen del sistema o de Docker Compose no se sobrescriben.
 *
 * Uso:
 * 1) Solo Config.php (Apache sin .env): no definas DB_HOST; edita el bloque else.
 * 2) PHP + .env sin Docker: define DB_HOST=localhost, DB_*, igual que el bloque else.
 * 3) Docker: Compose inyecta DB_HOST; .env solo rellena lo que Compose sustituye.
 */
if (!function_exists('cerroprieto_load_dotenv')) {
    function cerroprieto_load_dotenv(string $path): void
    {
        if (!is_readable($path)) {
            return;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            return;
        }
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || (isset($line[0]) && $line[0] === '#')) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v);
            if ($k === '') {
                continue;
            }
            if (strlen($v) >= 2 && (($v[0] === '"' && str_ends_with($v, '"')) || ($v[0] === "'" && str_ends_with($v, "'")))) {
                $v = substr($v, 1, -1);
            }
            if (getenv($k) !== false) {
                continue;
            }
            putenv($k . '=' . $v);
            $_ENV[$k] = $v;
        }
    }
}

cerroprieto_load_dotenv(__DIR__ . '/../.env');

$useEnv = getenv('DB_HOST') !== false && getenv('DB_HOST') !== '';

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
    define('cerro_prieto_series_imei', getenv('CERRO_PRIETO_SERIES_IMEI') ?: '860389053949943');
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
    define('url_nueva', 'http://161.132.53.51:9051');
    define('cerro_prieto_series_imei', '860389053949943');
}

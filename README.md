# Cerro Prieto

Sistema de telemetría y control para equipos de maduración y enfriamiento.

## Configuración: `Config.php`, `.env` y modos

`Config/Config.example.php` es la plantilla de `Config/Config.php`.

1. **Al arrancar**, si existe un archivo **`.env`** en la raíz del proyecto, se cargan sus variables con `putenv` **solo si** el sistema aún no las definió (Docker Compose sigue teniendo prioridad).

2. **Modo “variables de entorno”** (`if ($useEnv)`): se activa cuando **`DB_HOST` no está vacío** después de cargar `.env`. Ahí se usan `DB_*`, `BASE_URL`, `URLAPI_*`, etc. desde el entorno.

3. **Modo bloque `else`**: si **`DB_HOST` no existe o está vacío**, se usan las constantes fijas escritas en `Config.php` (típico en producción sin `.env` o con `.env` sin sección de base de datos).

| Escenario | Qué hacer |
|-----------|-----------|
| **Solo `Config.php` (práctica habitual sin Docker)** | Edita el bloque `else` en `Config/Config.php`. En `.env` **no** pongas `DB_HOST`, o comenta las líneas `DB_*` del staging. |
| **Apache + `.env` sin Docker** | En `.env` define `DB_HOST=localhost` y las mismas credenciales que quieras usar; `Config.php` las leerá. |
| **Docker staging** | `docker-compose.staging.yml` incluye **MySQL en el mismo Compose**. En `.env` usa `DB_HOST=mysql` (o déjalo: el compose fuerza `DB_HOST=mysql` en el servicio web). `DB_USER` / `DB_PASS` / `DB_NAME` deben coincidir con `MYSQL_*` del servicio mysql. |

**Error `1045` con `usuario@172.x.x.x`**: ocurre si la app en Docker habla con MySQL **en el host** (`host.docker.internal`): MySQL ve el cliente como red Docker, no como `localhost`, y el usuario suele estar definido solo para `localhost`. **Solución aplicada en staging:** MySQL dentro de Compose; la app usa `DB_HOST=mysql`. Si necesitas MySQL en el host, crea en MySQL un usuario con `GRANT ... TO 'usuario'@'%'` (o el rango `172.%`).

## Levantar con Docker (desarrollo)

Incluye MySQL local y esquema inicial (`Assets/bd/base.sql`).

```bash
docker compose up -d --build
```

- App: **http://localhost:8080** (puerto configurable con `WEB_PORT` en `.env`).
- Credenciales MySQL por defecto: usuario `cerroprieto`, base `zgroupot`.
- Login local si no defines `URLAPI_MYSQL`: usuario **admin** / contraseña **admin**.

Opcional: copia `.env.example` a `.env` y define `URLAPI_*` para usar la API como en producción.

## Entorno de prueba / staging en Docker

Incluye **web + MySQL** en la misma red (el servicio se llama `mysql`; la app usa `DB_HOST=mysql` y no depende de permisos `usuario@localhost` contra un MySQL en el host).

```bash
docker compose -f docker-compose.staging.yml up -d --build
```

- App: **http://localhost:8080** (o `WEB_PORT` en `.env`).
- MySQL expuesto en **3307** por defecto (`MYSQL_STAGING_PORT` en `.env`) para no chocar con un MySQL local en 3306.
- Define en `.env`: `DB_NAME`, `DB_USER`, `DB_PASS` (y opcional `MYSQL_ROOT_PASSWORD`). La base puede estar vacía al inicio; el login contra la API sigue funcionando si `URLAPI_MYSQL` está bien.
- Importar datos:  
  `docker exec -i cerroprieto-mysql-staging mysql -uztrack2023 -pTU_PASS zgroupztrack < respaldo.sql`
- Si cambiaste de un staging antiguo (solo web), borra el volumen anterior:  
  `docker compose -f docker-compose.staging.yml down -v` y vuelve a subir.

## Producción sin Docker

1. Ajusta `Config/Config.php` (bloque `else`): host, base de datos, usuario, contraseña, `base_url`, APIs.
2. Opciones para que **no** entre el modo env: no tengas `DB_HOST` en `.env`, o déjalo vacío / comentado; y no definas `DB_HOST` en Apache / PHP-FPM.
3. Si prefieres centralizar credenciales en `.env` con PHP en el mismo servidor, define `DB_HOST=localhost` y el resto de `DB_*` alineados con MySQL.

## Otros

- **Cambios en PHP sin reiniciar contenedor**: `docker/php-dev.ini` (OPcache revalidación).
- **Diagnóstico de APIs** (con sesión iniciada): ruta `AdminPage/diagnostico`.

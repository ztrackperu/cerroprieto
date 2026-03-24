#!/bin/bash
set -e

# Crear Config.php desde Config.example.php si no existe (útil con volumen montado)
if [ ! -f /var/www/html/Config/Config.php ]; then
    cp /var/www/html/Config/Config.example.php /var/www/html/Config/Config.php
fi

exec apache2-foreground

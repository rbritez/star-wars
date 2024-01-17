### PROYECTO SYMFONY - STAR WARS

# REQUISITOS
* php 8.1 o superior
* composer v2
* symfony-cli
* apache o nginx

# INSTALACION
* descargar el proyecto
* moverse a la raiz del proyecto y ejecutar:
```
  composer install
```
* crear el archivo .env mediante .env.example y luego editarla
```
cp .env.example .env
```
* levantar el proyecto con:
```
  symfony server:start
```
* probar test unitario:
```
vendor/bin/phpunit
```

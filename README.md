# promywe

## Despliegue con docker:
Instalación de docker.
```
$ apt install docker docker-compose
$ usermod -aG docker [usuario]
```
Usuario y contraseña de la base de datos:
```
username: root
password: toor
```

Generamos los contenedores:
```
$ docker-compose build [db/server]
```

Iniciamos los contenedores:
```
$ docker-compose up -d
```

Detenemos los contenedores:
```
$ docker-compose down
```
## Configuración de apache
```
<Directory />
    Options Indexes FollowSymLinks
    AllowOverride all
    Require all granted
</Directory>
```

## Instalación de depedencias mediante composer:
Instalamos composer.
```
$ apt install composer
```
Ejecutamos composer en la carpeta **./www/myweb**
```
$ composer install
```

## PhpMyAdmin
Descargamos phpmyadmin copiamos su carpeta en el directorio **./www/**

Modificamos el archivo **config.inc.php**
```
$cfg['blowfish_secret']
$cfg['Servers'][$i]['host']
$cfg['TempDir']
```

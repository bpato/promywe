# promywe

## Despliegue con docker:
Instalación de docker.
```
$ apt install docker docker-compose
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

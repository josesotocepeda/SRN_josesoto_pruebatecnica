# Prueba Técnica de Desarrollador Fullstack Backend

Correr composer, para bajar las librerías
```bash
composer install
```

Crear arhivo .env, desde consola correr el siguiente comando
```bash
cp env .env
```

# DOCKER
Pasos claros para clonar y levantar el proyecto usando Docker

Para poder correr docker deben configurar el archivo .env con los siguientes datos

```php
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------

CI_ENVIRONMENT = development

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------

database.default.hostname = db
database.default.database = todos_db
database.default.username = ciuser
database.default.password = cipass
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_general_ci
```

Construir la imagen
```bash
docker-compose up -d --build
```

Levantar contenedores: 
```bash
docker-compose up -d
```

Ingresar al contenedor de la app:
```bash
docker exec -it ci4_app bash
```

Ejecutar migraciones dentro del contenedor:
```bash
php spark migrate
```

Correr el proyecto
```bash
php spark serve
```

## Instrucciones para acceder al frontend.
Abrir en el navegador con la siguiente URL
```bash
http://localhost:8080
```


## Instrucciones para ejecutar las pruebas unitarias.
### Resumen de pruebas incluidas
. Crear tarea → POST /api/tasks debe responder 201 y devolver la tarea.

. Listar tareas → GET /api/tasks debe devolver un array de tareas.

. Actualizar tarea → PUT /api/tasks/{id} debe modificar título y estado.

Cómo ejecutar las tareas
```bash
vendor/bin/phpunit
```
o coorer el siguiente comando
```bash
vendor/bin/phpunit tests/app/Controllers/Api/TaskControllerTest.php
```

## Desarrollador
```bash
Jose Luis Soto
jose.luis.soto.cepeda@gmail.com
+569 8836 2440
```
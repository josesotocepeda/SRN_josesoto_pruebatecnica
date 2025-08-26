# Prueba Técnica de Desarrollador Fullstack Backend

## Pasos claros para clonar y levantar el proyecto usando Docker
# DOCKER
Para poder correr docker deben configurar el archivo .env con los siguientes datos

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


// Construir la imagen
docker-compose up -d --build

// Levantar contenedores: 
docker-compose up -d

// Ingresar al contenedor de la app: 
docker exec -it ci4_app bash

// Ejecutar migraciones dentro del contenedor:
php spark migrate

// Correr el proyecto
php spark serve


## Instrucciones para acceder al frontend.
// Abrir en el navegador: 
http://localhost:8080


## Instrucciones para ejecutar las pruebas unitarias.
### Resumen de pruebas incluidas
. Crear tarea → POST /api/tasks debe responder 201 y devolver la tarea.
. Listar tareas → GET /api/tasks debe devolver un array de tareas.
. Actualizar tarea → PUT /api/tasks/{id} debe modificar título y estado.

// Cómo ejecutar
vendor/bin/phpunit
o
vendor/bin/phpunit tests/app/Controllers/Api/TaskControllerTest.php
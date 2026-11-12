# GameStore

Aplicacion web de gestion y tienda de videojuegos con PHP, JavaScript y MySQL.

## Estructura

```text
app/
  config/       Configuracion de conexion a la base de datos
  controllers/  Endpoint de acciones del catalogo y del carrito
  models/       Espacio para modelos de datos
  views/        Vista principal de la aplicacion
public/
  css/          Hojas de estilo
  js/           Logica del cliente
  img/          Imagenes locales
database/       Consultas SQL
docs/           Documentacion del proyecto
tests/          Pruebas
index.php       Punto de entrada
README.md       Documentacion
```

## Ejecucion local

1. Coloca el proyecto en el directorio web de Apache.
2. Crea la base de datos `gamestore_db` en MySQL y configura las credenciales en `app/config/conexion.php`.
3. Abre `http://localhost/GameStore/` en el navegador.

La aplicacion puede mostrar el catalogo de respaldo si no hay conexion a la base
de datos. Las operaciones de escritura requieren una conexion activa.
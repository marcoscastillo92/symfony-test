## Asunciones
- El fichero CSV siempre va a venir con las mismas cabeceras en orden y valor
- Formato UTF-8
- En este caso asumimos que una row mal formada es una row con una fecha de estreno con formato diferente a Y-m-d
- No hay necesidad de autenticación de usuarios para usar la API

## Mejoras
- Crear una configuración en BD para la importación de diferentes formatos en los CSV (diferentes cabeceras y orden de columnas)
- Mejorar el test existente con un mock de los accesos a la BD o añadiendo la capacidad de guardar el estado previo al test y hacer rollback después.
- Crear/integrar un proceso asíncrono de importación (colas de trabajos)
- Centralizar las rutas en config/routes.yaml
- Optimizar tamaño del batch
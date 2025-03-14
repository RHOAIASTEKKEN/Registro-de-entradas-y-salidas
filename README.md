# Descripción del Código PHP

Este script PHP se utiliza para validar un código QR ingresado en un formulario, registrar la entrada de un usuario en una base de datos y enviar un correo electrónico al tutor del usuario con la información de entrada. Además, el script mueve los registros antiguos a una tabla de registros borrados y los elimina de la tabla original.

## Funcionamiento del Código

1. **Conexión a la base de datos:**
   - Se conecta a una base de datos MySQL utilizando las credenciales definidas.
   - Si la conexión falla, el script termina con un mensaje de error.

2. **Validación del código QR:**
   - El código QR ingresado en el formulario se escapa para prevenir inyecciones SQL.
   - Se realiza una consulta a la base de datos para verificar si el código QR existe en la tabla `usuarios`.

3. **Registro de entrada:**
   - Si el código QR es válido, se registra la entrada del usuario con la fecha y hora actuales en la tabla `registros`.

4. **Envío de correo electrónico:**
   - Se utiliza PHPMailer para enviar un correo electrónico al tutor del usuario.
   - El correo electrónico incluye el nombre del usuario, su código QR y la fecha de entrada.

5. **Gestión de registros:**
   - Se consulta la tabla `registros` para obtener todos los registros.
   - Para cada registro, se valida el usuario y se obtiene su correo electrónico.
   - Si se encuentra el correo del tutor, se envía un correo y luego el registro se mueve a la tabla `registrosborrados`.
   - Finalmente, el registro se elimina de la tabla `registros`.

6. **Manejo de errores:**
   - Si no se encuentra el correo del tutor o si ocurre un error al enviar el correo, se imprime un mensaje de error.
   - En caso de fallos al insertar o eliminar registros, también se imprime el error correspondiente.

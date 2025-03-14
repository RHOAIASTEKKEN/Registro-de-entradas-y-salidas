<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Establecer la zona horaria a Ciudad de México
date_default_timezone_set('America/Mexico_City');

// Aquí colocas las credenciales de tu base de datos
$host = '154.49.142.52';
$user = 'u811460462_java';
$password = '1m*I]4$C+K9';
$database = 'u811460462_java';
$port = 3306;

// Conexión a la base de datos
$conn = new mysqli($host, $user, $password, $database, $port);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el código QR del formulario
$qr = $conn->real_escape_string($_POST['qr']);

// Consulta SQL para validar el código QR en la tabla de usuarios
$sql = "SELECT * FROM usuarios WHERE codigo_qr = '$qr'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Si el código QR es válido, insertar un registro en la tabla de registros
    $usuario = $result->fetch_assoc()['usuario'];
    $fecha_entrada = date('Y-m-d H:i:s'); // Obtener la fecha actual

    $sql_insert = "INSERT INTO registros (usuario, entrada) VALUES ('$usuario', '$fecha_entrada')";
    if ($conn->query($sql_insert) === TRUE) {

        // Función para enviar correo electrónico
        function enviarCorreo($correo_tutor, $usuario, $nombre, $entrada)
        {
            $mail = new PHPMailer(true);
        
            try {
                //Configuración del servidor y del cliente
                $mail->SMTPDebug = 2;
                $mail->isSMTP();
                $mail->Host       = 'smtp.hostinger.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'asistencia@dreamgateways.net';
                $mail->Password   = 'War034272@';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
        
                //Destinatarios
                $mail->setFrom('asistencia@dreamgateways.net', 'Asistencia');
                $mail->addAddress($correo_tutor);
        
                //Contenido
                $mail->isHTML(true);
                $mail->Subject = 'Asunto del correo';
                $mail->Body    = 'ASISTENCIAL DEL USUARIO ' . $usuario . '<br> Buenas, señor padre o madre del usuario ' . $usuario . ', se le informa que la salida de la institucion de ' . $nombre . ' fue el día y hora señalados a continuación: ' . $entrada . '';
                $mail->send();
                return true; // Correo enviado exitosamente
            } catch (Exception $e) {
                echo "Error al enviar el correo: {$mail->ErrorInfo}<br>";
                return false; // Error al enviar el correo
            }
        }


        // Seleccionar registros nuevos
        $sql = "SELECT * FROM registros";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $entrada = $row['entrada'];
                $usuario = $row['usuario'];

                // Validar el usuario y obtener correo del tutor
                $sql_user = "SELECT correo_tutor, nombre FROM usuarios WHERE usuario = ?";
                $stmt = $conn->prepare($sql_user);
                $stmt->bind_param("s", $usuario);
                $stmt->execute();
                $result_user = $stmt->get_result();

                if ($result_user->num_rows > 0) {
                    $user = $result_user->fetch_assoc();
                    $nombre = $user['nombre'];
                    $correo_tutor = $user['correo_tutor'];
                    

                    // Enviar correo electrónico
                    if (enviarCorreo($correo_tutor, $usuario, $nombre, $entrada)) {
                        echo 'Mensaje enviado!';
                    } else {
                        echo 'Mensaje no enviado. Hubo un error.<br>';
                    }
                } else {
                    echo "No se encontró el correo del tutor para el usuario $usuario<br>";
                }

                // Insertar en registros borrados
                $sql_insert = "INSERT INTO registrosborrados (usuario, entrada) VALUES (?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("ss", $usuario, $entrada);
                if ($stmt_insert->execute()) {
                    echo "Registro insertado en registros borrados exitosamente<br>";
                } else {
                    echo "Error al insertar en registros borrados: " . $stmt_insert->error . "<br>";
                }

                // Eliminar el registro
                $sql_delete = "DELETE FROM registros WHERE id = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param("i", $row['id']);
                if ($stmt_delete->execute()) {
                    echo "Registro eliminado exitosamente<br>";
                } else {
                    echo "Error al eliminar el registro: " . $stmt_delete->error . "<br>";
                }

                header('Location: index.html');
            }
        } else {
            echo "No hay nuevos registros.<br>";
        }
    } else {
        echo "Error al insertar el registro: " . $conn->error;
    }
} else {
    echo "Código QR no válido.";
}

// Cerrar conexión
$conn->close();

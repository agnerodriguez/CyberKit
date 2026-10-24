<?php
// Desactivar la impresión de warnings/errors HTML en la salida JSON
ini_set('display_errors', 0);
error_reporting(0);

// Limpiar cualquier buffer o espacio en blanco previo
if (ob_get_length()) ob_clean();

include("conexion.php");

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonRecibido = file_get_contents('php://input');
    $datos = json_decode($jsonRecibido, true);

    if (json_last_error() === JSON_ERROR_NONE) {

        $recurso = $datos['recurso'] ?? '';
        $consulta = $datos['consulta'] ?? '';

        switch ($recurso) {
            case 'Usuarios':
                switch ($consulta) {
                    case 'Create': //Admin y usuario
                        $nombre = trim($datos['nombre'] ?? '');
                        $email = trim($datos['email'] ?? '');
                        $edad = (int)($datos['edad'] ?? 0);
                        $contrasena = $datos['contrasena'] ?? '';

                        // Validar que no estén vacíos
                        if ($nombre === '' || $email === '' || $edad <= 0 || $contrasena === '') {
                            http_response_code(400);
                            echo json_encode(["error" => "Todos los campos son obligatorios"]);
                            exit;
                        }

                        // Verificar si el email ya existe
                        $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $resultado = $stmt->get_result();

                        if ($resultado->num_rows > 0) {
                            http_response_code(409);
                            echo json_encode(["error" => "El email ya está registrado"]);
                            $stmt->close();
                            exit;
                        }
                        $stmt->close();

                        // Hashear la contraseña ANTES de insertar
                        $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);

                        // Insertar el usuario
                        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, edad, contrasena) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("ssis", $nombre, $email, $edad, $contrasena_hash);
                        $stmt->execute();
                        $id = $stmt->insert_id;
                        $stmt->close();

                        http_response_code(201);
                        echo json_encode([
                            "mensaje" => "Usuario creado correctamente",
                            "id_usuario" => $id,
                            "usuario" => $nombre,
                            "admin" => 0
                        ]);
                        break;
                    
                    case 'IniciarSesion': //Admin y usuario 
                        $email = trim($datos["email"] ?? '');
                        $contrasena = trim($datos["contrasena"] ?? '');

                        if ($email === '' || $contrasena === '') {
                            http_response_code(400);
                            echo json_encode(["error" => "Email y contraseña son obligatorios."]);
                            break;
                        }

                        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE email = ?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $resultado = $stmt->get_result();

                        if ($fila = $resultado->fetch_assoc()) {
                            // Verificar la contraseña con el hash guardado
                            if (password_verify($contrasena, $fila['contrasena'])) {
                                echo json_encode([
                                    "mensaje" => "El usuario ha ingresado correctamente",
                                    "idUsuario" => $fila['id_usuario'],
                                    "usuario" => $fila['nombre'],
                                    "admin" => (int)($fila['admin'] ?? 0)
                                ]);
                            } else {
                                http_response_code(401);
                                echo json_encode(["error" => "Contraseña incorrecta."]);
                            }
                        } else {
                            http_response_code(404);
                            echo json_encode(["error" => "Usuario no encontrado"]);
                        }
                        $stmt->close();
                        break;
                    
                    case 'ReadUsuarioByID': //Usuario
                        $id_usuario = (int)($datos['id_usuario'] ?? $_SESSION['id_usuario'] ?? 0);
                        
                        if ($id_usuario === 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "Usuario no encontrado."]);
                            break;
                        }
                        
                        $sql = "SELECT 
                                    u.id_usuario,
                                    u.nombre,
                                    u.email,
                                    u.edad,
                                    u.admin
                                FROM usuarios u
                                WHERE u.id_usuario = ?
                        ";
                        $stmt = $conexion->prepare($sql);
                        
                        if ($stmt) {
                            $stmt->bind_param("i", $id_usuario);
                            $stmt->execute();
                            $resultado = $stmt->get_result();
                            $usuario = $resultado->fetch_assoc();

                            if ($usuario) {
                                $usuario['admin'] = (int)($usuario['admin'] ?? 0);
                                echo json_encode($usuario);
                            } else {
                                http_response_code(404);
                                echo json_encode(["error" => "Usuario no encontrado."]);
                            }
                            $stmt->close();
                        } else {    
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error en la consulta.",
                                "detallesTecnicos" => $conexion->error
                            ]);
                        }
                        break;

                    case 'Update': //Usuario
                        $id_usuario = (int)($datos["id_usuario"] ?? $datos["id"] ?? 0);
                        $nombre = trim($datos["nombre"] ?? '');
                        $email = trim($datos["email"] ?? '');
                        $edad = (int)($datos["edad"] ?? 0);
                        $nueva_pass = trim($datos["contrasena"] ?? '');
                        $tiene_admin = isset($datos["admin"]);
                        $admin = $tiene_admin ? (int)$datos["admin"] : 0;

                        if ($id_usuario <= 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "ID de usuario requerido."]);
                            exit;
                        }

                        if (!empty($nueva_pass)) {
                            $contrasena = password_hash($nueva_pass, PASSWORD_BCRYPT);
                            if ($tiene_admin) {
                                $stmt = $conexion->prepare("UPDATE usuarios SET email = ?, nombre = ?, edad = ?, contrasena = ?, admin = ? WHERE id_usuario = ?");
                                $stmt->bind_param("ssisii", $email, $nombre, $edad, $contrasena, $admin, $id_usuario);
                            } else {
                                $stmt = $conexion->prepare("UPDATE usuarios SET email = ?, nombre = ?, edad = ?, contrasena = ? WHERE id_usuario = ?");
                                $stmt->bind_param("ssisi", $email, $nombre, $edad, $contrasena, $id_usuario);
                            }
                        } else {
                            if ($tiene_admin) {
                                $stmt = $conexion->prepare("UPDATE usuarios SET email = ?, nombre = ?, edad = ?, admin = ? WHERE id_usuario = ?");
                                $stmt->bind_param("ssiii", $email, $nombre, $edad, $admin, $id_usuario);
                            } else {
                                $stmt = $conexion->prepare("UPDATE usuarios SET email = ?, nombre = ?, edad = ? WHERE id_usuario = ?");
                                $stmt->bind_param("ssii", $email, $nombre, $edad, $id_usuario);
                            }
                        }
                        
                        if ($stmt->execute()) {
                            echo json_encode(["mensaje" => "Usuario actualizado correctamente."]);
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error: Usuario no actualizado.",
                                "detallesTecnicos" => $stmt->error
                            ]);
                        }
                        $stmt->close();
                        break;
                    
                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Consulta no válida"]);
                        break;
                }
                break;

            case 'Productos':
                switch ($consulta) {
                    case 'Create': // Admin
                        $nombre = trim($datos['nombre'] ?? '');
                        $descripcion = trim($datos['descripcion'] ?? '');
                        $stock = (int)($datos['stock'] ?? 0);
                        $monto = (float)($datos['monto'] ?? 0.0);
                        $imagenBase64 = $datos['imagen'] ?? null;

                        // Validar que el producto no exista por nombre
                        $stmt_check = $conexion->prepare("SELECT id_producto FROM productos WHERE nombre = ?");
                        $stmt_check->bind_param("s", $nombre);
                        $stmt_check->execute();
                        $res_check = $stmt_check->get_result();

                        if ($res_check->num_rows > 0) {
                            http_response_code(409);
                            echo json_encode(["error" => "El producto ya se encuentra almacenado en la base de datos."]);
                            $stmt_check->close();
                            exit;
                        }
                        $stmt_check->close();

                        // Validar que la imagen venga en el JSON (ya que la columna NO permite NULOS)
                        if (empty($imagenBase64)) {
                            http_response_code(400);
                            echo json_encode(["error" => "La imagen del producto es obligatoria."]);
                            exit;
                        }

                        $conexion->begin_transaction();

                        try {
                            // Insertar con una ruta temporal para cumplir con el NOT NULL de MySQL
                            $rutaTemporal = 'pendiente';
                            $stmt_prod = $conexion->prepare("INSERT INTO productos (nombre, descripcion, stock, imagen) VALUES (?, ?, ?, ?)");
                            $stmt_prod->bind_param("ssis", $nombre, $descripcion, $stock, $rutaTemporal);

                            if (!$stmt_prod->execute()) {
                                throw new Exception("Error al guardar el producto: " . $stmt_prod->error);
                            }

                            // Obtener el ID autogenerado
                            $nuevoIdProducto = $stmt_prod->insert_id;
                            $stmt_prod->close();

                            // Insertar el monto asociado
                            $stmt_monto = $conexion->prepare("INSERT INTO monto (id_producto, importe) VALUES (?, ?)");
                            $stmt_monto->bind_param("id", $nuevoIdProducto, $monto);

                            if (!$stmt_monto->execute()) {
                                throw new Exception("Error al guardar el monto del producto: " . $stmt_monto->error);
                            }
                            $stmt_monto->close();

                            // Procesar y guardar la imagen física en la carpeta productos/
                            $directorioDestino = 'productos/';

                            if (!is_dir($directorioDestino)) {
                                mkdir($directorioDestino, 0755, true);
                            }

                            $extension = 'jpg';
                            if (preg_match('/^data:image\/(\w+);base64,/', $imagenBase64, $coincidencias)) {
                                $extCapturada = strtolower($coincidencias[1]);
                                $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                                
                                if (in_array($extCapturada, $extensionesPermitidas)) {
                                    $extension = ($extCapturada === 'jpeg') ? 'jpg' : $extCapturada;
                                }
                                
                                $imagenBase64 = substr($imagenBase64, strpos($imagenBase64, ',') + 1);
                            }

                            $datosBinarios = base64_decode($imagenBase64);

                            if ($datosBinarios === false) {
                                throw new Exception("El formato de la imagen Base64 no es válido.");
                            }

                            $nombreArchivo = 'producto_' . $nuevoIdProducto . '.' . $extension;
                            $rutaBD = $directorioDestino . $nombreArchivo;

                            // Escribir archivo en disco
                            if (file_put_contents($rutaBD, $datosBinarios) === false) {
                                throw new Exception("No se pudo escribir la imagen en la carpeta productos.");
                            }

                            // Actualizar el registro sustituyendo 'pendiente' por la ruta final
                            $stmt_img = $conexion->prepare("UPDATE productos SET imagen = ? WHERE id_producto = ?");
                            $stmt_img->bind_param("si", $rutaBD, $nuevoIdProducto);
                            
                            if (!$stmt_img->execute()) {
                                throw new Exception("Error al actualizar la ruta final de la imagen: " . $stmt_img->error);
                            }
                            $stmt_img->close();

                            $conexion->commit();

                            http_response_code(201);
                            echo json_encode([
                                "mensaje" => "Producto registrado correctamente.",
                                "id_producto" => $nuevoIdProducto,
                                "nombre" => $nombre,
                                "stock" => $stock,
                                "monto" => $monto,
                                "imagen" => $rutaBD
                            ]);

                        } catch (Exception $e) {
                            $conexion->rollback();

                            // Limpiar archivo físico si se llegó a crear antes del error
                            if (isset($rutaBD) && file_exists($rutaBD)) {
                                unlink($rutaBD);
                            }

                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al registrar el producto.",
                                "detallesTecnicos" => $e->getMessage()
                            ]);
                        }
                        break;
                    
                    case 'ReadAdmin': //Admin
                        $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.stock, p.imagen, m.importe AS monto
                                FROM productos p
                                INNER JOIN monto m ON p.id_producto = m.id_producto
                                ORDER BY p.id_producto DESC";
                        $resultado = $conexion->query($sql);

                        if ($resultado) {
                            $productos = [];
                            while ($fila = $resultado->fetch_assoc()) {
                                $productos[] = [
                                    "id_producto" => (int)$fila['id_producto'],
                                    "nombre" => $fila['nombre'],
                                    "descripcion" => $fila['descripcion'],
                                    "stock" => (int)$fila['stock'],
                                    "monto" => (float)$fila['monto'],
                                    "imagen" => $fila['imagen']
                                ];
                            }
                            http_response_code(200);
                            echo json_encode($productos);
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al obtener la lista de productos.",
                                "detallesTecnicos" => $conexion->error
                            ]);
                        }
                        break;

                    case 'ReadUsuario': //Usuario
                        $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.stock, p.imagen, m.importe AS monto
                                FROM productos p
                                INNER JOIN monto m ON p.id_producto = m.id_producto
                                WHERE p.stock > 0
                                ORDER BY p.nombre ASC";
                        $resultado = $conexion->query($sql);

                        if ($resultado) {
                            $productos = [];
                            while ($fila = $resultado->fetch_assoc()) {
                                $productos[] = [
                                    "id_producto" => (int)$fila['id_producto'],
                                    "nombre" => $fila['nombre'],
                                    "descripcion" => $fila['descripcion'],
                                    "stock" => (int)$fila['stock'],
                                    "monto" => (float)$fila['monto'],
                                    "imagen" => $fila['imagen']
                                ];
                            }
                            http_response_code(200);
                            echo json_encode($productos);
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al obtener la lista de productos.",
                                "detallesTecnicos" => $conexion->error
                            ]);
                        }
                        break;

                    case 'ReadProductoByID': //Usuario
                        $id_producto = (int)($datos['id_producto'] ?? $datos['id'] ?? 0);

                        if ($id_producto <= 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "Producto no encontrado."]);
                            exit;
                        }

                        $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.stock, p.imagen, m.importe AS monto
                                FROM productos p
                                INNER JOIN monto m ON p.id_producto = m.id_producto
                                WHERE p.id_producto = ?";

                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param("i", $id_producto);
                        $stmt->execute();
                        $resultado = $stmt->get_result();

                        if ($fila = $resultado->fetch_assoc()) {
                            http_response_code(200);
                            echo json_encode([
                                "id_producto" => (int)$fila['id_producto'],
                                "nombre" => $fila['nombre'],
                                "descripcion" => $fila['descripcion'],
                                "stock" => (int)$fila['stock'],
                                "monto" => (float)$fila['monto'],
                                "imagen" => $fila['imagen']
                            ]);
                        } else {
                            http_response_code(404);
                            echo json_encode(["error" => "Producto no encontrado."]);
                        }

                        $stmt->close();
                        break;
                
                    case 'Update': //Admin
                        $id_producto = (int)($datos['id_producto'] ?? $datos['id'] ?? 0);
                        $nombre = trim($datos['nombre'] ?? '');
                        $descripcion = trim($datos['descripcion'] ?? '');
                        $stock = (int)($datos['stock'] ?? 0);
                        $monto = (float)($datos['monto'] ?? 0.0);
                        $imagenBase64 = $datos['imagen'] ?? null;

                        if ($id_producto <= 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "Producto no encontrado."]);
                            exit;
                        }

                        $stmt_check = $conexion->prepare("SELECT id_producto FROM productos WHERE nombre = ? AND id_producto != ?");
                        $stmt_check->bind_param("si", $nombre, $id_producto);
                        $stmt_check->execute();
                        $res_check = $stmt_check->get_result();

                        if ($res_check->num_rows > 0) {
                            http_response_code(409);
                            echo json_encode(["error" => "Ya existe otro producto registrado con ese nombre."]);
                            $stmt_check->close();
                            exit;
                        }
                        $stmt_check->close();

                        $conexion->begin_transaction();

                        try {
                            $rutaBD = null;

                            // Si enviaron una nueva imagen en Base64 para actualizar
                            if (!empty($imagenBase64) && strpos($imagenBase64, 'productos/') === false) {
                                $directorioDestino = 'productos/';

                                if (!is_dir($directorioDestino)) {
                                    mkdir($directorioDestino, 0755, true);
                                }

                                $extension = 'jpg';
                                if (preg_match('/^data:image\/(\w+);base64,/', $imagenBase64, $coincidencias)) {
                                    $extCapturada = strtolower($coincidencias[1]);
                                    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                                    if (in_array($extCapturada, $extensionesPermitidas)) {
                                        $extension = ($extCapturada === 'jpeg') ? 'jpg' : $extCapturada;
                                    }
                                    $imagenBase64 = substr($imagenBase64, strpos($imagenBase64, ',') + 1);
                                }

                                $datosBinarios = base64_decode($imagenBase64);

                                if ($datosBinarios !== false) {
                                    $nombreArchivo = 'producto_' . $id_producto . '.' . $extension;
                                    $rutaBD = $directorioDestino . $nombreArchivo;
                                    file_put_contents($rutaBD, $datosBinarios);
                                }
                            }

                            if ($rutaBD !== null) {
                                $stmt_prod = $conexion->prepare("UPDATE productos SET nombre = ?, descripcion = ?, stock = ?, imagen = ? WHERE id_producto = ?");
                                $stmt_prod->bind_param("ssisi", $nombre, $descripcion, $stock, $rutaBD, $id_producto);
                            } else {
                                $stmt_prod = $conexion->prepare("UPDATE productos SET nombre = ?, descripcion = ?, stock = ? WHERE id_producto = ?");
                                $stmt_prod->bind_param("ssii", $nombre, $descripcion, $stock, $id_producto);
                            }

                            if (!$stmt_prod->execute()) {
                                throw new Exception("Error al actualizar el producto: " . $stmt_prod->error);
                            }
                            $stmt_prod->close();

                            $stmt_monto = $conexion->prepare("UPDATE monto SET importe = ? WHERE id_producto = ?");
                            $stmt_monto->bind_param("di", $monto, $id_producto);

                            if (!$stmt_monto->execute()) {
                                throw new Exception("Error al actualizar el monto del producto: " . $stmt_monto->error);
                            }
                            $stmt_monto->close();

                            $conexion->commit();

                            http_response_code(200);
                            echo json_encode([
                                "mensaje" => "Producto actualizado correctamente.",
                                "id_producto" => $id_producto,
                                "nombre" => $nombre,
                                "stock" => $stock,
                                "monto" => $monto
                            ]);

                        } catch (Exception $e) {
                            $conexion->rollback();
                            error_log("Error interno: " . $e->getMessage());

                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al actualizar el producto.",
                                "detallesTecnicos" => $e->getMessage()
                            ]);
                        }
                        break;
                    
                    case 'Delete': //Admin
                        $id_producto = (int)($datos['id_producto'] ?? $datos['id'] ?? 0);

                        if ($id_producto <= 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "Producto no encontrado."]);
                            exit;
                        }

                        // Obtener la ruta de la imagen antes de borrar para eliminar el archivo físico
                        $stmt_check = $conexion->prepare("SELECT imagen FROM productos WHERE id_producto = ?");
                        $stmt_check->bind_param("i", $id_producto);
                        $stmt_check->execute();
                        $res_check = $stmt_check->get_result();

                        if ($res_check->num_rows === 0) {
                            http_response_code(404);
                            echo json_encode(["error" => "El producto que intenta eliminar no existe."]);
                            $stmt_check->close();
                            exit;
                        }

                        $fila = $res_check->fetch_assoc();
                        $rutaImagen = $fila['imagen'] ?? null;
                        $stmt_check->close();

                        $stmt = $conexion->prepare("DELETE FROM productos WHERE id_producto = ?");
                        $stmt->bind_param("i", $id_producto);

                        if ($stmt->execute()) {
                            // Borrar la imagen de la carpeta física si existía
                            if ($rutaImagen && file_exists($rutaImagen)) {
                                unlink($rutaImagen);
                            }

                            http_response_code(200);
                            echo json_encode([
                                "mensaje" => "Producto eliminado correctamente.",
                                "id_producto" => $id_producto
                            ]);
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al intentar eliminar el producto.",
                                "detallesTecnicos" => $stmt->error
                            ]);
                        }

                        $stmt->close();
                        break;

                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Consulta no válida"]);
                        break;
                }
                break;
            
            case 'DetalleCompra':
                switch ($consulta) {
                    case 'Create': //Usuario
                        $id_producto = (int)($datos['id_producto'] ?? 0);
                        $id_usuario = (int)($datos['id_usuario'] ?? 0);
                        $id_estado = (int)($datos['estado'] ?? $datos['id_estado'] ?? 3); 
                        $cantidad = (int)($datos['cantidad'] ?? 0);
                        $imagen_comprobante = $datos['comprobante'] ?? null;

                        if ($id_producto <= 0 || $id_usuario <= 0 || $id_estado <= 0 || $cantidad <= 0 || empty($imagen_comprobante)) {
                            http_response_code(400);
                            echo json_encode(["error" => "Datos no válidos o falta el comprobante de pago."]);
                            exit;
                        }

                        $conexion->begin_transaction();

                        try {
                            $stmt_comp = $conexion->prepare("INSERT INTO comprobante (imagen) VALUES (?)");
                            $stmt_comp->bind_param("s", $imagen_comprobante);

                            if (!$stmt_comp->execute()) {
                                throw new Exception("Error al guardar el comprobante de pago: " . $stmt_comp->error);
                            }

                            $id_comprobante = $stmt_comp->insert_id;
                            $stmt_comp->close();

                            $stmt_monto = $conexion->prepare("SELECT id_monto, importe FROM monto WHERE id_producto = ?");
                            $stmt_monto->bind_param("i", $id_producto);
                            $stmt_monto->execute();
                            $res_monto = $stmt_monto->get_result();

                            if ($res_monto->num_rows === 0) {
                                throw new Exception("El producto no tiene un precio/monto asignado.");
                            }

                            $datos_monto = $res_monto->fetch_assoc();
                            $id_monto = (int)$datos_monto['id_monto'];
                            $precio_unitario = (float)$datos_monto['importe'];
                            $stmt_monto->close();

                            $subtotal = $precio_unitario * $cantidad;

                            $sql = "INSERT INTO detalleCompra (id_producto, id_usuario, id_monto, id_comprobante, id_estado, cantidad, subtotal) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)";
                            
                            $stmt_detalle = $conexion->prepare($sql);
                            $stmt_detalle->bind_param("iiiiiid", $id_producto, $id_usuario, $id_monto, $id_comprobante, $id_estado, $cantidad, $subtotal);

                            if (!$stmt_detalle->execute()) {
                                throw new Exception("Se ha producido un error durante el registro de la compra: " . $stmt_detalle->error);
                            }

                            $id_detalleCompra = $stmt_detalle->insert_id;
                            $stmt_detalle->close();

                            $stmt_stock = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ? AND stock >= ?");
                            $stmt_stock->bind_param("iii", $cantidad, $id_producto, $cantidad);
                            $stmt_stock->execute();

                            if ($stmt_stock->affected_rows === 0) {
                                throw new Exception("Stock insuficiente para realizar la compra.");
                            }
                            $stmt_stock->close();

                            $conexion->commit();

                            http_response_code(201);
                            echo json_encode([
                                "mensaje" => "Registro de pago/compra creado exitosamente.",
                                "id_detalleCompra" => $id_detalleCompra,
                                "id_producto" => $id_producto,
                                "id_usuario" => $id_usuario,
                                "id_monto" => $id_monto,
                                "id_comprobante" => $id_comprobante,
                                "id_estado" => $id_estado,
                                "cantidad" => $cantidad,
                                "subtotal" => $subtotal
                            ]);

                        } catch (Exception $e) {
                            $conexion->rollback();

                            error_log("Error interno: " . $e->getMessage());

                            http_response_code(500);
                            echo json_encode([
                                "error"            => "Error al procesar la compra.",
                                "detallesTecnicos" => $e->getMessage()
                            ]);
                        }
                        break;
                    
                    case 'Read': //Admin
                        $sql = "SELECT 
                                    dc.id_detalleCompra,
                                    dc.cantidad,
                                    dc.subtotal,
                                    dc.fecha,
                                    p.id_producto,
                                    p.nombre AS producto_nombre,
                                    p.imagen AS producto_imagen,
                                    u.id_usuario,
                                    u.nombre AS usuario_nombre,
                                    u.email AS usuario_email,
                                    m.importe AS precio_unitario,
                                    dc.id_comprobante,
                                    dc.id_estado
                                FROM detalleCompra dc
                                INNER JOIN productos p ON dc.id_producto = p.id_producto
                                INNER JOIN usuarios u ON dc.id_usuario = u.id_usuario
                                INNER JOIN monto m ON dc.id_monto = m.id_monto
                                ORDER BY dc.id_detalleCompra DESC";

                        $resultado = $conexion->query($sql);

                        if ($resultado) {
                            $detalles = [];
                            while ($fila = $resultado->fetch_assoc()) {
                                $detalles[] = [
                                    "id_detalleCompra" => (int)$fila['id_detalleCompra'],
                                    "cantidad" => (int)$fila['cantidad'],
                                    "subtotal" => (float)$fila['subtotal'],
                                    "fecha" => $fila['fecha'],
                                    "producto" => [
                                        "id_producto" => (int)$fila['id_producto'],
                                        "nombre" => $fila['producto_nombre'],
                                        "imagen" => $fila['producto_imagen'] // Corregido: envía la ruta limpia almacenada
                                    ],
                                    "usuario" => [
                                        "id_usuario" => (int)$fila['id_usuario'],
                                        "nombre" => $fila['usuario_nombre'],
                                        "email"  => $fila['usuario_email']
                                    ],
                                    "precio_unitario" => (float)$fila['precio_unitario'],
                                    "id_comprobante" => (int)$fila['id_comprobante'],
                                    "id_estado" => (int)$fila['id_estado']
                                ];
                            }

                            http_response_code(200);
                            echo json_encode($detalles);
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al obtener la lista de compras.",
                                "detallesTecnicos" => $conexion->error
                            ]);
                        }
                        break;

                    case 'Update': //Admin
                        $id_detalleCompra = (int)($datos['id_detalleCompra'] ?? 0);
                        $id_estado  = (int)($datos['id_estado'] ?? 0);

                        if ($id_detalleCompra <= 0 || $id_estado <= 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "Datos de actualización no válidos."]);
                            exit;
                        }

                        $conexion->begin_transaction();

                        try {
                            $stmt_upd = $conexion->prepare("UPDATE detalleCompra SET id_estado = ? WHERE id_detalleCompra = ?");
                            $stmt_upd->bind_param("ii", $id_estado, $id_detalleCompra);

                            if (!$stmt_upd->execute()) {
                                throw new Exception("Error al actualizar el estado de la compra: " . $stmt_upd->error);
                            }

                            if ($stmt_upd->affected_rows === 0) {
                                throw new Exception("No se encontró la compra o ya fue actualizada.");
                            }
                            $stmt_upd->close();

                            $conexion->commit();
                            
                            http_response_code(200);
                            echo json_encode([
                                "mensaje" => "Estado de la compra actualizado correctamente.",
                                "id_detalleCompra" => $id_detalleCompra,
                                "id_estado" => $id_estado
                            ]);

                        } catch (Exception $e) {
                            $conexion->rollback();

                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al actualizar el estado de la compra.",
                                "detallesTecnicos" => $e->getMessage()
                            ]);
                        }
                        break;

                    case 'enviarMail': 
                        require_once 'PHPMailer/src/Exception.php';
                        require_once 'PHPMailer/src/PHPMailer.php';
                        require_once 'PHPMailer/src/SMTP.php';

                        $id_detalleCompra = (int)($datos['id_detalleCompra'] ?? 0);

                        if ($id_detalleCompra <= 0) {
                            http_response_code(400);
                            echo json_encode([
                                "error" => "No se pudo identificar la compra a notificar.",
                                "detallesTecnicos" => "ID de compra no válido o ausente."
                            ]);
                            exit;
                        }

                        $sql = "SELECT 
                                    dc.id_detalleCompra,
                                    dc.subtotal,
                                    p.nombre AS producto_nombre,
                                    u.nombre AS usuario_nombre,
                                    u.email AS usuario_email,
                                    e.nombre AS estado_nombre
                                FROM detalleCompra dc
                                INNER JOIN productos p ON dc.id_producto = p.id_producto
                                INNER JOIN usuarios u ON dc.id_usuario = u.id_usuario
                                INNER JOIN estado e ON dc.id_estado = e.id_estado
                                WHERE dc.id_detalleCompra = ?";

                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param("i", $id_detalleCompra);
                        $stmt->execute();
                        $resultado = $stmt->get_result();

                        if ($fila = $resultado->fetch_assoc()) {
                            $emailUsuario = $fila['usuario_email'];
                            $nombreUsuario = $fila['usuario_nombre'];
                            $productoNombre = $fila['producto_nombre'];
                            $subtotal = $fila['subtotal'];
                            $estadoNombre = $fila['estado_nombre'];

                            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

                            try {
                                $mail->CharSet = 'UTF-8';
                                $mail->Encoding = 'base64';
                                $mail->isSMTP();
                                $mail->Host       = 'smtp.gmail.com';
                                $mail->SMTPAuth   = true;
                                $mail->Username   = 'recytech.soporte@gmail.com';
                                $mail->Password   = ''; 
                                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                                $mail->Port       = 587;

                                $mail->setFrom('recytech.soporte@gmail.com', 'RecyTech');
                                $mail->addAddress($emailUsuario, $nombreUsuario);

                                $mail->isHTML(true);
                                $mail->Subject = 'Estado de tu compra - RecyTech';
                                $mail->Body    = '
                                <!DOCTYPE html>
                                <html lang="es">
                                <head>
                                <meta charset="UTF-8">
                                </head>
                                <body style="font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 20px;">
                                <div style="max-width: 500px; background-color: #ffffff; padding: 20px; border-radius: 8px; margin: 0 auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                    <h2 style="color: #0d8a42;">Notificación de Compra</h2>
                                    <p>Hola <b>' . htmlspecialchars($nombreUsuario) . '</b>,</p>
                                    <p>Te informamos sobre el estado actualizado de tu orden de compra <b>#' . $id_detalleCompra . '</b>:</p>
                                    
                                    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                                    <tr>
                                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><b>Producto:</b></td>
                                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($productoNombre) . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><b>Total:</b></td>
                                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">$' . number_format($subtotal, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><b>Estado:</b></td>
                                        <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold; color: #0d8a42;">' . htmlspecialchars($estadoNombre) . '</td>
                                    </tr>
                                    </table>

                                    <p style="margin-top: 20px; font-size: 13px; color: #666;">Si tienes alguna duda con respecto a tu pago, por favor ponte en contacto con nuestro equipo mediante el foro de nuestro sitio.</p>
                                </div>
                                </body>
                                </html>';

                                $mail->send();

                                http_response_code(200);
                                echo json_encode(["mensaje" => "Correo enviado exitosamente al usuario."]);

                            } catch (Exception $e) {
                                http_response_code(500);
                                echo json_encode([
                                    "error" => "No se pudo enviar el correo de notificación.",
                                    "detallesTecnicos" => $mail->ErrorInfo
                                ]);
                            }
                        } else {
                            http_response_code(404);
                            echo json_encode([
                                "error" => "La compra solicitada no existe.",
                                "detallesTecnicos" => "No se encontró ningún registro en detalleCompra con el ID: " . $id_detalleCompra
                            ]);
                        }
                        $stmt->close();
                        break;
                    
                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Consulta no válida"]);
                        break;
                }
                break;

            case 'Publicacion':
                switch ($consulta) {
                    case 'Create': // Usuario
                        $id_usuario = (int)($datos['id_usuario'] ?? 0);
                        $id_categoria = (int)($datos['id_categoria'] ?? 0);
                        $contenido = trim($datos['contenido'] ?? '');
                        $imagen = $datos['imagen'] ?? null;

                        if ($id_usuario <= 0 || $id_categoria <= 0 || empty($contenido)) {
                            http_response_code(400);
                            echo json_encode(["error" => "Datos incompletos para crear la publicación."]);
                            exit;
                        }

                        $stmt = $conexion->prepare("INSERT INTO publicacion (id_usuario, id_categoria, contenido, imagen) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("iiss", $id_usuario, $id_categoria, $contenido, $imagen);

                        if ($stmt->execute()) {
                            $id_publicacion = $stmt->insert_id;
                            http_response_code(201);
                            echo json_encode([
                                "mensaje" => "Publicación creada exitosamente.",
                                "id_publicacion" => $id_publicacion,
                                "id_usuario" => $id_usuario,
                                "id_categoria" => $id_categoria
                            ]);
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al crear la publicación.",
                                "detallesTecnicos" => $stmt->error
                            ]);
                        }
                        $stmt->close();
                        break;
                    
                    case 'Read': // Usuario
                        $id_categoria = (int)($datos['id_categoria'] ?? 0);

                        if ($id_categoria <= 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "Categoría requerida."]);
                            exit;
                        }

                        $sql = "SELECT p.id_publicacion, p.contenido, p.imagen, p.fecha, u.id_usuario, u.nombre AS autor_nombre, p.id_categoria
                                FROM publicacion p
                                INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
                                WHERE p.id_categoria = ?
                                ORDER BY p.id_publicacion DESC";

                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param("i", $id_categoria);
                        
                        if ($stmt->execute()) {
                            $resultado = $stmt->get_result();
                            $publicaciones = [];

                            while ($fila = $resultado->fetch_assoc()) {
                                $publicaciones[] = [
                                    "id_publicacion" => (int)$fila['id_publicacion'],
                                    "contenido" => $fila['contenido'],
                                    "fecha" => $fila['fecha'],
                                    "id_usuario" => (int)$fila['id_usuario'],
                                    "autor_nombre" => $fila['autor_nombre'],
                                    "id_categoria" => (int)$fila['id_categoria'],
                                    "imagen" => $fila['imagen'] ? base64_encode($fila['imagen']) : null
                                ];
                            }

                            http_response_code(200);
                            echo json_encode($publicaciones);
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al obtener las publicaciones.",
                                "detallesTecnicos" => $stmt->error
                            ]);
                        }
                        $stmt->close();    
                        break;

                    case 'Delete': //Usuario
                        $id_publicacion = (int)($datos['id_publicacion'] ?? 0);

                        if ($id_publicacion <= 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "ID de publicación no válido."]);
                            exit;
                        }

                        $stmt = $conexion->prepare("DELETE FROM publicacion WHERE id_publicacion = ?");
                        $stmt->bind_param("i", $id_publicacion);
                        if ($stmt->execute()) {
                            if ($stmt->affected_rows > 0) {
                                http_response_code(200);
                                echo json_encode([
                                    "mensaje" => "Publicación eliminada correctamente.",
                                    "id_publicacion" => $id_publicacion
                                ]);
                            } else {
                                http_response_code(404);
                                echo json_encode(["error" => "La publicación no existe o ya fue eliminada."]);
                            }
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al eliminar la publicación.",
                                "detallesTecnicos" => $stmt->error
                            ]);
                        }
                        $stmt->close();
                        break;
                    
                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Consulta no válida"]);
                        break;
                }
                break;

            case 'Comentario':
                switch ($consulta) {
                    case 'Create': // Usuario
                        $id_publicacion = (int)($datos['id_publicacion'] ?? 0);
                        $id_usuario = (int)($datos['id_usuario'] ?? 0);
                        $contenido = trim($datos['contenido'] ?? '');
                        $imagen = $datos['imagen'] ?? null;
                        $id_comentario_padre = (int)($datos['id_comentario_padre'] ?? 0);

                        if ($id_publicacion <= 0 || $id_usuario <= 0 || empty($contenido)) {
                            http_response_code(400);
                            echo json_encode(["error" => "Datos incompletos para publicar el comentario."]);
                            exit;
                        }

                        // Corregido: sintaxis de consulta y bind_param alineadas
                        if ($id_comentario_padre <= 0) {
                            $sql = "INSERT INTO comentario (id_publicacion, id_usuario, contenido, imagen) VALUES (?, ?, ?, ?)";
                            $stmt = $conexion->prepare($sql);
                            $stmt->bind_param("iiss", $id_publicacion, $id_usuario, $contenido, $imagen);
                        } else {
                            $sql = "INSERT INTO comentario (id_publicacion, id_usuario, contenido, imagen, id_comentario_padre) VALUES (?, ?, ?, ?, ?)";
                            $stmt = $conexion->prepare($sql);
                            $stmt->bind_param("iissi", $id_publicacion, $id_usuario, $contenido, $imagen, $id_comentario_padre);
                        }

                        if ($stmt->execute()) {
                            $id_comentario = $stmt->insert_id;
                            http_response_code(201);
                            echo json_encode([
                                "mensaje" => "Comentario publicado exitosamente.",
                                "id_comentario" => $id_comentario,
                                "id_publicacion" => $id_publicacion,
                                "id_usuario" => $id_usuario,
                                "id_comentario_padre" => $id_comentario_padre > 0 ? $id_comentario_padre : null
                            ]);
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al guardar el comentario.",
                                "detallesTecnicos" => $stmt->error
                            ]);
                        }
                        $stmt->close();
                    break;
                    
                    case 'Read': // Usuario
                        $id_publicacion = (int)($datos['id_publicacion'] ?? 0);

                        if ($id_publicacion <= 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "ID de publicación no válido o requerido."]);
                            exit;
                        }

                        $sql = "SELECT c.id_comentario, c.id_publicacion, c.contenido, c.imagen, c.id_comentario_padre, c.fecha, u.id_usuario, u.nombre AS autor_nombre
                                FROM comentario c
                                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                                WHERE c.id_publicacion = ?
                                ORDER BY c.fecha ASC";

                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param("i", $id_publicacion);

                        if ($stmt->execute()) {
                            $resultado = $stmt->get_result();
                            $comentarios = [];

                            while ($fila = $resultado->fetch_assoc()) {
                                $comentarios[] = [
                                    "id_comentario" => (int)$fila['id_comentario'],
                                    "id_publicacion" => (int)$fila['id_publicacion'],
                                    "contenido" => $fila['contenido'],
                                    "id_comentario_padre" => $fila['id_comentario_padre'] !== null ? (int)$fila['id_comentario_padre'] : null,
                                    "fecha" => $fila['fecha'],
                                    "id_usuario" => (int)$fila['id_usuario'],
                                    "autor_nombre" => $fila['autor_nombre'],
                                    "imagen" => $fila['imagen']
                                ];
                            }

                            http_response_code(200);
                            echo json_encode($comentarios);
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al obtener los comentarios.",
                                "detallesTecnicos" => $stmt->error
                            ]);
                        }
                        $stmt->close();
                        break;

                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Consulta no válida"]);
                        break;
                }
                break;
            
            case 'Tutoriales':
                switch ($consulta) {
                    case 'Read': //Admin
                        $sql = "SELECT id_tutorial, nombre, descripcion, archivo FROM tutoriales ORDER BY id_tutorial DESC";

                        $resultado = $conexion->query($sql);

                        if ($resultado) {
                            $detalles = [];
                            while ($fila = $resultado->fetch_assoc()) {
                                $detalles[] = [
                                    "id_tutorial" => (int)$fila['id_tutorial'],
                                    "nombre" => $fila['nombre'],
                                    "descripcion" => $fila['descripcion'],
                                    "archivo" => $fila['archivo'],
                                ];
                            }

                            http_response_code(200);
                            echo json_encode($detalles);
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al mostrar los tutoriales.",
                                "detallesTecnicos" => $conexion->error
                            ]);
                        }
                        break;
                    
                    case 'ReadByID': //Usuario
                        $id_tutorial = (int)($datos['id_tutorial'] ?? $datos['id'] ?? 0);

                        if ($id_tutorial <= 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "Tutorial no encontrado."]);
                            exit;
                        }

                        $sql = "SELECT id_tutorial, nombre, descripcion, archivo FROM tutoriales WHERE id_tutorial = ?";

                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param("i", $id_tutorial);
                        $stmt->execute();
                        $resultado = $stmt->get_result();

                        if ($fila = $resultado->fetch_assoc()) {
                            http_response_code(200);
                            echo json_encode([
                                "id_tutorial" => (int)$fila['id_tutorial'],
                                "nombre" => $fila['nombre'],
                                "descripcion" => $fila['descripcion'],
                                "archivo" => $fila['archivo'],
                            ]);
                        } else {
                            http_response_code(404);
                            echo json_encode(["error" => "Tutorial no encontrado."]);
                        }

                        $stmt->close();
                        break;

                    case 'Create': //Admin
                        $nombre = trim($datos['nombre'] ?? '');
                        $descripcion = trim($datos['descripcion'] ?? '');
                        $archivo = trim($datos['archivo'] ?? '');

                        $stmt = $conexion->prepare("INSERT INTO tutoriales (nombre, descripcion, archivo) VALUES (?, ?, ?)");
                        $stmt->bind_param("sss", $nombre, $descripcion, $archivo);

                        if ($stmt->execute()) {
                            $id_tutorial = $stmt->insert_id;
                            http_response_code(201);
                            echo json_encode([
                                "mensaje" => "Tutorial agregado correctamente.",
                                "id_tutorial" => $id_tutorial,
                                "nombre" => $nombre,
                                "descripcion" => $descripcion,
                                "archivo" => $archivo
                            ]);
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al agregar el tutorial.",
                                "detallesTecnicos" => $stmt->error
                            ]);
                        }
                        $stmt->close();
                        break;

                    case 'Update': //Admin
                        $id_tutorial = (int)($datos['id_tutorial'] ?? $datos['id'] ?? 0);
                        $nombre = trim($datos['nombre'] ?? '');
                        $descripcion = trim($datos['descripcion'] ?? '');
                        $archivo = trim($datos['archivo'] ?? '');
                        
                        if ($id_tutorial <= 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "Tutorial no encontrado."]);
                            exit;
                        }

                        $stmt_check = $conexion->prepare("SELECT id_tutorial FROM tutoriales WHERE nombre = ? AND id_tutorial != ?");
                        $stmt_check->bind_param("si", $nombre, $id_tutorial);
                        $stmt_check->execute();
                        $res_check = $stmt_check->get_result();

                        if ($res_check->num_rows > 0) {
                            http_response_code(409);
                            echo json_encode(["error" => "Ya existe otro tutorial registrado con ese nombre."]);
                            $stmt_check->close();
                            exit;
                        }
                        $stmt_check->close();

                        $stmt = $conexion->prepare("UPDATE tutoriales SET nombre = ?, descripcion = ?, archivo = ? WHERE id_tutorial = ?");
                        $stmt->bind_param("sssi", $nombre, $descripcion, $archivo, $id_tutorial);

                        if ($stmt->execute()) {
                            echo json_encode([
                                "mensaje" => "Tutorial actualizado correctamente."
                            ]);
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error: Tutorial no actualizado.",
                                "detallesTecnicos" => $stmt->error
                            ]);
                        }
                        $stmt->close();
                        break;

                    case 'Delete': //Admin
                        $id_tutorial = (int)($datos['id_tutorial'] ?? $datos['id'] ?? 0);

                        if ($id_tutorial <= 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "ID de tutorial no válido."]);
                            exit;
                        }

                        $stmt = $conexion->prepare("DELETE FROM tutoriales WHERE id_tutorial = ?");
                        $stmt->bind_param("i", $id_tutorial);
                        if ($stmt->execute()) {
                            if ($stmt->affected_rows > 0) {
                                http_response_code(200);
                                echo json_encode([
                                    "mensaje" => "Tutorial eliminado correctamente.",
                                    "id_tutorial" => $id_tutorial
                                ]);
                            } else {
                                http_response_code(404);
                                echo json_encode(["error" => "El tutorial no existe o ya fue eliminado."]);
                            }
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error al eliminar el tutorial.",
                                "detallesTecnicos" => $stmt->error
                            ]);
                        }
                        $stmt->close();
                        break;
                    
                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Consulta no válida"]);
                        break;
                }
                break;
            
            default:
                http_response_code(400);
                echo json_encode(["error" => "Recurso no válido"]);
                break;
        }   
            
            
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "El JSON enviado no es válido."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido."]);
} 

$conexion->close();
?>
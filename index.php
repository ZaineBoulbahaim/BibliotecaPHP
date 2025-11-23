<?php
// 🔧 CARGA AUTOMÁTICA DE CLASES - PSR-4
// Carga todas las clases automáticamente sin necesidad de requires manuales
require_once 'autoload.php';

// 🎪 INICIO DE SESIÓN
// Permite persistir datos entre diferentes páginas del sistema
session_start();

// 🐛 CONFIGURACIÓN DE DEBUG
// Muestra todos los errores (útil en desarrollo, quitar en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ============================================================================
// 🏛️  INICIALIZACIÓN DEL SISTEMA - PATRÓN SINGLETON VÍA SESIÓN
// ============================================================================

// Verifica si ya existe una biblioteca en la sesión actual
if (isset($_SESSION['biblioteca']) && $_SESSION['biblioteca'] instanceof Biblioteca) {
    // ✅ RECUPERA la biblioteca existente de la sesión
    // Esto mantiene el estado entre navegaciones del usuario
    $biblioteca = $_SESSION['biblioteca'];
} else {
    // ✅ INICIALIZA una nueva biblioteca si no existe
    // Esto ocurre en la primera visita o al resetear la sesión
    $biblioteca = new Biblioteca("Biblioteca Central");
    
    // 🗄️ GUARDA la biblioteca en sesión para persistencia
    $_SESSION['biblioteca'] = $biblioteca;

    // ============================================================================
    // 📚 CREACIÓN DE MATERIALES DE EJEMPLO - DEMOSTRACIÓN DE HERENCIA
    // ============================================================================

    // 📖 LIBROS - Heredan de Material e implementan Reservable
    $llibres = [
        // Cada libro: ID, Título, Autor, Año, Páginas
        new Llibre(1, "El Quijote", "Miguel de Cervantes", 1605, 863),
        new Llibre(2, "Cien Años de Soledad", "Gabriel García Márquez", 1967, 417),
        new Llibre(3, "1984", "George Orwell", 1949, 328),
        new Llibre(4, "Moby Dick", "Herman Melville", 1851, 635),
    ];

    // 💿 DVDs - Heredan de Material e implementan Reservable
    $dvds = [
        // Cada DVD: ID, Título, Director, Año, Duración (minutos)
        new DVD(5, "Inception", "Christopher Nolan", 2010, 148),
        new DVD(6, "Interstellar", "Christopher Nolan", 2014, 169),
        new DVD(7, "The Matrix", "The Wachowskis", 1999, 136),
        new DVD(8, "Parasite", "Bong Joon-ho", 2019, 132),
    ];

    // 📰 REVISTAS - Heredan de Material pero NO implementan Reservable
    $revistes = [
        // Cada revista: ID, Título, Autor, Año, Número Edición
        new Revista(9, "National Geographic", "Varios", 2023, 200),
        new Revista(10, "Time", "Varios", 2023, 125),
        new Revista(11, "Scientific American", "Varios", 2023, 150),
        new Revista(12, "The Economist", "Varios", 2023, 120),
    ];

    // 🔄 AÑADE todos los materiales a la biblioteca usando polimorfismo
    // array_merge() combina los tres arrays en uno solo
    foreach (array_merge($llibres, $dvds, $revistes) as $material) {
        // ✅ Polimorfismo: afegirMaterial() acepta cualquier objeto Material
        $biblioteca->afegirMaterial($material);
    }

    // ============================================================================
    // 👥 CREACIÓN DE USUARIOS DE EJEMPLO - DEMOSTRACIÓN DE VALIDACIÓN
    // ============================================================================

    // 🧍 USUARIOS - Demuestran métodos mágicos y validación
    $usuaris = [
        // Cada usuario: Nombre, Email (se valida automáticamente)
        new Usuari("Alice", "alice@example.com"),
        new Usuari("Bob", "bob@example.com"),
        new Usuari("Carlos", "carlos@example.com"),
        new Usuari("Diana", "diana@example.com")
    ];
    
    // ➕ AÑADE todos los usuarios al sistema
    foreach ($usuaris as $u) {
        $biblioteca->afegirUsuari($u);
    }
}

// ============================================================================
// 🎮 GESTIÓN DE ACCIONES - PATRÓN FRONT CONTROLLER
// ============================================================================

// Verifica si se ha solicitado alguna acción mediante GET
if (isset($_GET['action'])) {
    // 🔍 OBTIENE parámetros de la acción con valores por defecto
    $materialId = (int)($_GET['material_id'] ?? 0);  // Convierte a int por seguridad
    $usuariNom = $_GET['usuari'] ?? '';              // Valor por defecto string vacío
    
    // 🔎 BUSCA el material y usuario correspondientes
    $material = $biblioteca->cercarPerId($materialId);
    $usuari = $biblioteca->cercarUsuari($usuariNom);

    // 🚨 MANEJO DE EXCEPCIONES - Control centralizado de errores
    try {
        // 🎯 SWITCH como router básico - Determina qué acción ejecutar
        switch ($_GET['action']) {
            case 'prestar':
                // 📥 ACCIÓN PRESTAR - Verifica existencia de material y usuario
                if ($material && $usuari) {
                    // ✅ Intenta realizar el préstamo
                    if ($biblioteca->prestarMaterial($materialId, $usuari)) {
                        $_SESSION['msg'] = "✅ Préstamo realizado con éxito";
                    }
                } else {
                    // ❌ Material o usuario no encontrados
                    $_SESSION['error'] = "❌ Material o usuario no encontrado";
                }
                break;

            case 'retornar':
                // 📤 ACCIÓN DEVOLVER - Solo necesita el material
                if ($material) {
                    if ($biblioteca->retornarMaterial($materialId)) {
                        $_SESSION['msg'] = "✅ Material devuelto con éxito";
                    } else {
                        $_SESSION['error'] = "❌ No se pudo devolver el material";
                    }
                } else {
                    $_SESSION['error'] = "❌ Material no encontrado";
                }
                break;

            case 'reservar':
                // 📅 ACCIÓN RESERVAR - Solo para materiales Reservable
                if ($material && $usuari && $material instanceof Reservable) {
                    // ✅ Type checking: instanceof verifica que sea reservable
                    if ($material->reservar($usuari->nom)) {
                        $_SESSION['msg'] = "✅ Reserva realizada con éxito";
                    } else {
                        $_SESSION['error'] = "❌ No se pudo realizar la reserva";
                    }
                } else {
                    $_SESSION['error'] = "❌ Material no reservable o datos incorrectos";
                }
                break;

            case 'cancelar_reserva':
                // ❌ ACCIÓN CANCELAR RESERVA - Solo para materiales Reservable
                if ($material && $material instanceof Reservable) {
                    if ($material->cancelarReserva()) {
                        $_SESSION['msg'] = "✅ Reserva cancelada con éxito";
                    } else {
                        $_SESSION['error'] = "❌ No se pudo cancelar la reserva";
                    }
                } else {
                    $_SESSION['error'] = "❌ Material no reservable o no encontrado";
                }
                break;
        }
    } catch (Exception $e) {
        // 🚨 CAPTURA cualquier excepción no controlada
        $_SESSION['error'] = "❌ Error: " . $e->getMessage();
    }
    
    // 🔄 REDIRECCIÓN POST-REDIRECT-GET - Evita reenvíos de formulario
    // Redirige a la página actual o a materiales por defecto
    header("Location: index.php?page=" . ($_GET['page'] ?? 'materials'));
    exit;  // ⚠️ Termina la ejecución después de redirigir
}

// ============================================================================
// 🧭 SISTEMA DE ROUTING - GESTIÓN DE PÁGINAS
// ============================================================================

// 📄 OBTIENE parámetros de la URL con valores por defecto
$page = $_GET['page'] ?? 'home';    // Página solicitada (home por defecto)
$type = $_GET['type'] ?? null;      // Tipo de material para filtros
$id = $_GET['id'] ?? null;          // ID para detalles específicos

// ============================================================================
// 🎨 FUNCIÓN AUXILIAR - RENDERIZADO DE TABLA DE MATERIALES
// ============================================================================

/**
 * 📊 Renderiza una tabla HTML con los materiales proporcionados
 * 
 * @param array $materials Lista de materiales a mostrar
 * @param Biblioteca $biblioteca Instancia para obtener usuarios
 * @return void
 */
function renderMaterialsTable(array $materials, Biblioteca $biblioteca): void {
    // ✅ PHP 8: Typed parameters (array y Biblioteca)
    
    // 🕳️ Caso vacío - Mensaje amigable
    if (empty($materials)) {
        echo "<p>No hay materiales para mostrar.</p>";
        return;  // Termina la función early
    }

    // 🏗️ ESTRUCTURA DE LA TABLA
    echo "<table class='table'>
        <thead>
            <tr><th>ID</th><th>Título</th><th>Autor</th><th>Tipo</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>";

    // 🔄 ITERA sobre cada material
    foreach ($materials as $m) {
        // 🛡️ OBTIENE y escapa datos para seguridad XSS
        $id = $m->getId();
        $titol = htmlspecialchars($m->getTitol());  // 🔒 Previene XSS
        $autor = htmlspecialchars($m->getAutor());  // 🔒 Previene XSS
        $tipus = htmlspecialchars($m->getTipus());  // 🔒 Previene XSS
        
        // 🎭 RENDERIZADO CONDICIONAL DEL ESTADO
        $estado = $m->isDisponible() ? 
            "<span class='badge success'>Disponible</span>" :   // ✅ Verde
            "<span class='badge danger'>Prestado</span>";       // ❌ Rojo
        
        // 📍 INFORMACIÓN DE RESERVA (solo para Reservable)
        $reservaInfo = "";
        if ($m instanceof Reservable && $m->estaReservat()) {
            // 🔒 Escapa también el nombre del usuario de reserva
            $reservaInfo = "<br><small class='reserva-info'>Reservado por: " . 
                          htmlspecialchars($m->getUsuariReserva()) . "</small>";
        }

        // 🏷️ FILA DE LA TABLA
        echo "<tr>
            <td>{$id}</td>
            <td>{$titol}</td>
            <td>{$autor}</td>
            <td>{$tipus}</td>
            <td>{$estado}{$reservaInfo}</td>
            <td class='actions'>";

        // 🎯 ACCIONES DISPONIBLES BASADAS EN ESTADO
        if ($m->isDisponible()) {
            // 📥 FORMULARIO PRÉSTAMO (solo si disponible)
            echo "<form method='get' class='inline-form'>
                    <input type='hidden' name='action' value='prestar'>
                    <input type='hidden' name='material_id' value='{$id}'>
                    <input type='hidden' name='page' value='materials'>
                    <select name='usuari' required>
                        <option value=''>Seleccionar usuario</option>";
            
            // 👥 GENERA opciones de usuarios dinámicamente
            foreach ($biblioteca->getUsuaris() as $u) {
                $nom = htmlspecialchars($u->nom);  // 🔒 Escapa nombres
                echo "<option value='{$nom}'>{$nom}</option>";
            }
            echo "</select>
                    <button type='submit' class='btn btn-primary'>Prestar</button>
                  </form>";

            // 📅 FORMULARIO RESERVA (solo para Reservable)
            if ($m instanceof Reservable) {
                echo "<form method='get' class='inline-form'>
                        <input type='hidden' name='action' value='reservar'>
                        <input type='hidden' name='material_id' value='{$id}'>
                        <input type='hidden' name='page' value='materials'>
                        <select name='usuari' required>
                            <option value=''>Seleccionar usuario</option>";
                foreach ($biblioteca->getUsuaris() as $u) {
                    $nom = htmlspecialchars($u->nom);
                    echo "<option value='{$nom}'>{$nom}</option>";
                }
                echo "</select>
                        <button type='submit' class='btn btn-warning'>Reservar</button>
                      </form>";
            }
        } else {
            // 📤 BOTÓN DEVOLVER (si no está disponible)
            echo "<a href='?action=retornar&material_id={$id}&page=materials' class='btn btn-danger'>Devolver</a>";
            
            // ❌ BOTÓN CANCELAR RESERVA (si está reservado)
            if ($m instanceof Reservable && $m->estaReservat()) {
                echo " <a href='?action=cancelar_reserva&material_id={$id}&page=materials' class='btn btn-secondary'>Cancelar Reserva</a>";
            }
        }

        echo "</td></tr>";
    }

    // 🏁 CIERRE DE LA TABLA
    echo "</tbody></table>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema Biblioteca Digital</title>
    <style>
        /* 🎨 ESTILOS CSS MODERNOS */
        
        /* 🎯 ESTILOS GENERALES */
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f4f6f8; 
            color: #333; 
            margin:0; 
            padding:0; 
        }
        
        /* 🧭 BARRA DE NAVEGACIÓN */
        .navbar { 
            background-color: #3f51b5; 
            padding: 15px; 
            display:flex; 
            gap:15px; 
            flex-wrap: wrap;  /* 📱 Responsive */
        }
        .navbar a { 
            color:white; 
            text-decoration:none; 
            padding:8px 15px; 
            border-radius:4px; 
            transition: background-color 0.3s;  /* 🎪 Animación suave */
        }
        .navbar a:hover { 
            background-color: #303f9f;  /* 🎨 Efecto hover */
        }
        
        /* 📦 CONTENEDOR PRINCIPAL */
        .container { 
            padding:20px; 
            max-width:1400px; 
            margin:auto;  /* 🎯 Centrado */
        }
        
        /* 📊 TABLAS */
        table { 
            width:100%; 
            border-collapse:collapse; 
            margin-bottom:20px; 
            background:white; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);  /* 🎭 Sombra suave */
        }
        table, th, td { 
            border:1px solid #ddd;  /* 🎨 Borde sutil */
        }
        th, td { 
            padding:12px; 
            text-align:left; 
        }
        th { 
            background-color:#f8f9fa; 
            font-weight: bold; 
        }
        
        /* 🏷️ BADGES (etiquetas de estado) */
        .badge { 
            padding: 4px 8px; 
            border-radius: 4px; 
            color: white; 
            font-size: 12px; 
            font-weight: bold; 
        }
        .badge.success { background-color: #28a745; }  /* ✅ Verde éxito */
        .badge.danger { background-color: #dc3545; }   /* ❌ Rojo peligro */
        .badge.warning { background-color: #ffc107; color: black; }  /* ⚠️ Amarillo advertencia */
        
        /* 🔘 BOTONES */
        .btn { 
            padding: 8px 15px; 
            border: none; 
            border-radius: 4px; 
            text-decoration: none; 
            display: inline-block; 
            cursor: pointer; 
            font-size: 14px; 
            transition: opacity 0.3s;  /* 🎪 Animación */
        }
        .btn:hover { opacity: 0.9; }  /* 🎨 Efecto hover */
        .btn-primary { background-color: #007bff; color: white; }    /* 🔵 Primario */
        .btn-warning { background-color: #ffc107; color: black; }    /* 🟡 Advertencia */
        .btn-danger { background-color: #dc3545; color: white; }     /* 🔴 Peligro */
        .btn-secondary { background-color: #6c757d; color: white; }  /* ⚫ Secundario */
        
        /* 📝 FORMULARIOS EN LÍNEA */
        .inline-form { 
            display: inline-block; 
            margin-right: 10px; 
            margin-bottom: 5px; 
        }
        .actions { 
            min-width: 350px;  /* 🎯 Ancho mínimo para acciones */
        }
        
        /* 💬 ALERTAS (mensajes de feedback) */
        .alert { 
            padding: 12px; 
            margin: 15px 0; 
            border-radius: 4px; 
        }
        .alert-success { 
            background-color: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb; 
        }
        .alert-error { 
            background-color: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb; 
        }
        
        /* 📈 CUADROS DE ESTADÍSTICAS (Grid CSS) */
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));  /* 🎯 Responsive */
            gap: 20px; 
            margin: 20px 0; 
        }
        .stat-card { 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        .stat-card h3 { 
            margin-top: 0; 
            color: #3f51b5;  /* 🎨 Color primario */
        }
        .number { 
            font-size: 2em; 
            font-weight: bold; 
            color: #3f51b5; 
        }
        
        /* 📍 INFORMACIÓN DE RESERVA */
        .reserva-info { 
            color: #e67e22; 
            font-style: italic; 
        }
        
        /* 📋 LISTAS DE ELEMENTOS */
        .list-group { 
            background: white; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        .list-item { 
            padding: 10px 0; 
            border-bottom: 1px solid #eee;  /* 🎨 Separadores */
        }
        .list-item:last-child { 
            border-bottom: none;  /* 🎯 Último elemento sin borde */
        }
        
        /* 📝 SELECTS (desplegables) */
        select { 
            padding: 6px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            margin-right: 5px; 
        }
    </style>
</head>
<body>
<!-- 🧭 BARRA DE NAVEGACIÓN PRINCIPAL -->
<nav class="navbar">
    <a href="index.php">🏠 Inici</a>  <!-- 🏠 Icono casa -->
    <a href="index.php?page=materials">📚 Materials</a>  <!-- 📚 Icono libros -->
    <a href="index.php?page=usuaris">👥 Usuaris</a>      <!-- 👥 Icono usuarios -->
    <a href="index.php?page=prestecs">🔄 Préstecs</a>    <!-- 🔄 Icono intercambio -->
    <a href="index.php?page=estadistiques">📊 Stats</a>  <!-- 📊 Icono gráfica -->
    <a href="index.php?page=auditoria">📝 Auditoria</a>  <!-- 📝 Icono lista -->
    <a href="index.php?page=nou_usuari">➕ Nou Usuari</a> <!-- ➕ Icono añadir -->
    <a href="reset_session.php" style="margin-left: auto; background-color: #dc3545;">🔄 Reset Session</a> <!-- 🔄 Reset en rojo -->
</nav>

<!-- 📦 CONTENEDOR PRINCIPAL DEL CONTENIDO -->
<div class="container">
<?php
// 💬 SISTEMA DE MENSAJES DE FEEDBACK
// Muestra mensajes de éxito/error almacenados en sesión

// ✅ MENSAJE DE ÉXITO
if (isset($_SESSION['msg'])) {
    echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['msg']) . "</div>";
    unset($_SESSION['msg']);  // 🗑️ Limpia el mensaje después de mostrarlo
}

// ❌ MENSAJE DE ERROR  
if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-error'>" . htmlspecialchars($_SESSION['error']) . "</div>";
    unset($_SESSION['error']);  // 🗑️ Limpia el error después de mostrarlo
}

// ============================================================================
// 🎯 ROUTER PRINCIPAL - GESTIÓN DE PÁGINAS
// ============================================================================

// 🗺️ SWITCH que determina qué contenido mostrar según la página solicitada
switch ($page) {
    // 🏠 PÁGINA DE INICIO - DASHBOARD
    case 'home':
        echo "<h1>🏠 Dashboard Biblioteca Digital</h1>";
        
        // 📊 OBTIENE estadísticas en tiempo real
        $estadistiques = $biblioteca->obtenirEstadistiques();
        
        // 🎨 CUADROS DE ESTADÍSTICAS (Grid CSS)
        echo "<div class='stats-grid'>";
        
        // 📦 CUADRO: Total de materiales
        echo "<div class='stat-card'><h3>Total Materials</h3><span class='number'>{$estadistiques['total']}</span></div>";
        
        // 📦 CUADRO: Materiales disponibles
        echo "<div class='stat-card'><h3>Disponibles</h3><span class='number'>{$estadistiques['disponibles']}</span></div>";
        
        // 📦 CUADRO: Materiales prestados
        echo "<div class='stat-card'><h3>Prestados</h3><span class='number'>{$estadistiques['prestats']}</span></div>";
        
        // 📦 CUADRO: Total de usuarios
        echo "<div class='stat-card'><h3>Usuarios</h3><span class='number'>{$estadistiques['usuaris']}</span></div>";
        
        // 📦 CUADRO: Distribución por tipo (lista)
        echo "<div class='stat-card'><h3>Materiales por Tipo</h3><ul style='list-style:none; padding:0; margin:0;'>";
        foreach ($estadistiques['perTipus'] as $tipo => $cantidad) {
            // 🔒 Escapa ambos valores por seguridad
            echo "<li><strong>" . htmlspecialchars($tipo) . ":</strong> " . htmlspecialchars((string)$cantidad) . "</li>";
        }
        echo "</ul></div>";
        
        echo "</div>";  // 🏁 Cierre del grid
        break;

    // 📚 PÁGINA DE MATERIALES - CON FILTROS
    case 'materials':
        echo "<h1>📚 Gestión de Materiales</h1>";
        
        // 🎯 BARRA DE FILTROS
        echo "<div style='margin: 20px 0;'>";
        // 🔘 Botones de filtro con estado activo/inactivo
        echo "<a href='?page=materials' class='btn " . (!$type ? 'btn-primary' : 'btn-secondary') . "'>Todos</a> ";
        echo "<a href='?page=materials&type=llibre' class='btn " . ($type === 'llibre' ? 'btn-primary' : 'btn-secondary') . "'>Libros</a> ";
        echo "<a href='?page=materials&type=dvd' class='btn " . ($type === 'dvd' ? 'btn-primary' : 'btn-secondary') . "'>DVDs</a> ";
        echo "<a href='?page=materials&type=revista' class='btn " . ($type === 'revista' ? 'btn-primary' : 'btn-secondary') . "'>Revistas</a>";
        echo "</div>";

        // 🎯 SELECCIÓN DE MATERIALES SEGÚN FILTRO
        if ($type === 'llibre') {
            $materialsToShow = $biblioteca->getLlibres();  // 📖 Solo libros
            echo "<h2>📖 Libros (" . count($materialsToShow) . ")</h2>";
        } elseif ($type === 'dvd') {
            $materialsToShow = $biblioteca->getDVDs();     // 💿 Solo DVDs
            echo "<h2>💿 DVDs (" . count($materialsToShow) . ")</h2>";
        } elseif ($type === 'revista') {
            $materialsToShow = $biblioteca->getRevistes(); // 📰 Solo revistas
            echo "<h2>📰 Revistas (" . count($materialsToShow) . ")</h2>";
        } else {
            $materialsToShow = $biblioteca->getMaterials(); // 📚 Todos los materiales
            echo "<h2>📚 Todos los Materiales (" . count($materialsToShow) . ")</h2>";
        }

        // 🎨 RENDERIZA la tabla con los materiales seleccionados
        renderMaterialsTable($materialsToShow, $biblioteca);
        break;

    // 📋 PÁGINA DE DETALLE DE MATERIAL
    case 'material':
        // 🎯 DETALLE DE MATERIAL ESPECÍFICO
        if ($id !== null) {
            $material = $biblioteca->cercarPerId((int)$id);  // 🔍 Busca por ID
            if ($material) {
                echo "<h1>📋 Detalle del Material</h1>";
                
                // 📝 LISTA DE INFORMACIÓN GENERAL
                echo "<div class='list-group'>";
                echo "<div class='list-item'><strong>ID:</strong> " . $material->getId() . "</div>";
                echo "<div class='list-item'><strong>Título:</strong> " . htmlspecialchars($material->getTitol()) . "</div>";
                echo "<div class='list-item'><strong>Autor:</strong> " . htmlspecialchars($material->getAutor()) . "</div>";
                echo "<div class='list-item'><strong>Año de Publicación:</strong> " . $material->getAnyPublicacio() . "</div>";
                echo "<div class='list-item'><strong>Tipo:</strong> " . htmlspecialchars($material->getTipus()) . "</div>";
                echo "<div class='list-item'><strong>Estado:</strong> " . 
                     ($material->isDisponible() ? 
                         "<span class='badge success'>Disponible</span>" : 
                         "<span class='badge danger'>Prestado</span>") . 
                     "</div>";
                
                // 🎯 INFORMACIÓN ESPECÍFICA SEGÚN TIPO (instanceof)
                if ($material instanceof Llibre) {
                    echo "<div class='list-item'><strong>Número de Páginas:</strong> " . $material->getNumeroPagines() . "</div>";
                } elseif ($material instanceof DVD) {
                    echo "<div class='list-item'><strong>Duración:</strong> " . $material->getDuracio() . " minutos</div>";
                    echo "<div class='list-item'><strong>Duración Formateada:</strong> " . $material->getDuracioFormatada() . "</div>";
                } elseif ($material instanceof Revista) {
                    echo "<div class='list-item'><strong>Número de Edición:</strong> " . $material->getNumeroEdicio() . "</div>";
                }
                
                // 📍 INFORMACIÓN DE RESERVA (solo para Reservable)
                if ($material instanceof Reservable) {
                    echo "<div class='list-item'><strong>Reserva:</strong> " . 
                         ($material->estaReservat() ? 
                             "<span class='badge warning'>Reservado por: " . htmlspecialchars($material->getUsuariReserva()) . "</span>" : 
                             "<span class='badge success'>No reservado</span>") . 
                         "</div>";
                }
                
                echo "</div>";  // 🏁 Cierre lista información
                
                // 📝 HISTORIAL DE AUDITORÍA (del trait Auditoria)
                echo "<h2>📝 Historial de Acciones</h2>";
                $historial = $material->obtenirHistorial();
                if ($historial) {
                    echo "<ul class='list-group'>";
                    foreach ($historial as $registro) {
                        echo "<li class='list-item'>
                                <strong>" . htmlspecialchars($registro['accio']) . "</strong> - " . 
                                htmlspecialchars($registro['detalls']) . " - 
                                <em>" . htmlspecialchars($registro['data']) . "</em>
                              </li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No hay historial de acciones para este material.</p>";
                }
                
                // ⚡ ACCIONES DISPONIBLES
                echo "<h2>⚡ Acciones</h2>";
                echo "<div style='margin: 15px 0;'>";
                if ($material->isDisponible()) {
                    echo "<a href='?page=materials' class='btn btn-primary'>Prestar este material</a> ";
                } else {
                    echo "<a href='?action=retornar&material_id=" . $material->getId() . "&page=material&id=" . $material->getId() . "' class='btn btn-danger'>Devolver material</a> ";
                }
                
                // 🎯 ACCIONES DE RESERVA (solo para Reservable)
                if ($material instanceof Reservable) {
                    if ($material->estaReservat()) {
                        echo "<a href='?action=cancelar_reserva&material_id=" . $material->getId() . "&page=material&id=" . $material->getId() . "' class='btn btn-warning'>Cancelar Reserva</a>";
                    } else {
                        echo "<a href='?page=materials' class='btn btn-warning'>Reservar este material</a>";
                    }
                }
                echo "</div>";
                
            } else {
                // ❌ Material no encontrado
                echo "<p class='alert alert-error'>Material no encontrado.</p>";
            }
        } else {
            // ❌ ID no proporcionado
            echo "<p class='alert alert-error'>No se ha proporcionado un ID de material.</p>";
        }
        break;

    // 🔄 PÁGINA DE PRÉSTAMOS ACTIVOS
    case 'prestecs':
        echo "<h1>🔄 Préstecs Actius</h1>";
        $prestecsActius = $biblioteca->getPrestecsActius();
        
        if ($prestecsActius) {
            echo "<table class='table'>
                <thead>
                    <tr><th>Material</th><th>Usuario</th><th>Fecha Préstamo</th><th>Días Pendientes</th><th>Estado</th><th>Acciones</th></tr>
                </thead>
                <tbody>";
            
            // 🔄 ITERA sobre cada préstamo activo
            foreach ($prestecsActius as $p) {
                $material = $p->getMaterial();
                $usuari = $p->getUsuari();
                $diesPendents = $p->getDiesPendents();
                
                // 🎭 ESTADO DEL PRÉSTAMO (vencido/en plazo)
                $estado = $p->estaVencut() ? 
                    "<span class='badge danger'>Vencido (" . $p->calcularDiesRetard() . " días)</span>" : 
                    "<span class='badge success'>En plazo ($diesPendents días)</span>";
                
                echo "<tr>
                    <td>{$material->getTitol()} ({$material->getTipus()})</td>
                    <td>{$usuari->nom}</td>
                    <td>{$p->getDataPrestec()->format('d/m/Y')}</td>  // 📅 Formato español
                    <td>{$diesPendents}</td>
                    <td>{$estado}</td>
                    <td>
                        <a href='?action=retornar&material_id={$material->getId()}&page=prestecs' class='btn btn-danger'>Devolver</a>
                    </td>
                </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No hay préstamos activos en este momento.</p>";
        }
        break;

    // 👥 PÁGINA DE LISTADO DE USUARIOS
    case 'usuaris':
        echo "<h1>👥 Listado de Usuarios</h1>";
        $usuaris = $biblioteca->getUsuaris();
        
        if ($usuaris) {
            echo "<table class='table'>
                <thead><tr><th>Nombre</th><th>Email</th><th>Materiales Prestados</th><th>Fecha Registro</th><th>Acciones</th></tr></thead>
                <tbody>";
            
            // 🔄 ITERA sobre cada usuario
            foreach ($usuaris as $u) {
                $nom = htmlspecialchars($u->nom);
                $email = htmlspecialchars($u->email);
                $numPrestados = $u->getNumeroMaterialsPrestat();
                $fechaRegistro = $u->dataRegistre->format('d/m/Y');  // 📅 Formato español
                
                // 🎭 BADGE de materiales prestados (color según cantidad)
                echo "<tr>
                    <td>{$nom}</td>
                    <td>{$email}</td>
                    <td><span class='badge " . ($numPrestados > 0 ? 'warning' : 'success') . "'>{$numPrestados}</span></td>
                    <td>{$fechaRegistro}</td>
                    <td>
                        <a href='index.php?page=usuari&id=" . urlencode($nom) . "' class='btn btn-primary'>Ver Perfil</a>
                    </td>
                </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No hay usuarios registrados.</p>";
        }
        break;

    // 👤 PÁGINA DE PERFIL DE USUARIO
    case 'usuari':
        if ($id !== null) {
            $usuari = $biblioteca->cercarUsuari($id);  // 🔍 Busca usuario por nombre
            if ($usuari) {
                echo "<h1>👤 Perfil de " . htmlspecialchars($usuari->nom) . "</h1>";
                
                // 📝 INFORMACIÓN DEL USUARIO
                echo "<div class='list-group'>";
                echo "<div class='list-item'><strong>Email:</strong> " . htmlspecialchars($usuari->email) . "</div>";
                echo "<div class='list-item'><strong>Fecha de Registro:</strong> " . $usuari->dataRegistre->format('d/m/Y H:i') . "</div>";
                echo "<div class='list-item'><strong>Total Materiales Prestados:</strong> " . $usuari->getNumeroMaterialsPrestat() . "</div>";
                echo "</div>";

                // 📚 MATERIALES PRESTADOS ACTUALMENTE
                echo "<h2>📚 Materiales Actualmente Prestados</h2>";
                $materialsPrestados = $usuari->getMaterialsPrestat();
                
                if ($materialsPrestados) {
                    echo "<ul class='list-group'>";
                    foreach ($materialsPrestados as $m) {
                        echo "<li class='list-item'>
                                <strong>{$m->getTitol()}</strong> ({$m->getTipus()}) - 
                                <em>{$m->getAutor()}</em> ({$m->getAnyPublicacio()})
                              </li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>El usuario no tiene materiales prestados actualmente.</p>";
                }
            } else {
                echo "<p class='alert alert-error'>Usuario no encontrado.</p>";
            }
        } else {
            echo "<p class='alert alert-error'>No se ha proporcionado un ID de usuario.</p>";
        }
        break;

    // 📊 PÁGINA DE ESTADÍSTICAS
    case 'estadistiques':
        echo "<h1>📊 Estadísticas del Sistema</h1>";
        $estadistiques = $biblioteca->obtenirEstadistiques();
        
        echo "<div class='stats-grid'>";
        foreach ($estadistiques as $k => $v) {
            echo "<div class='stat-card'><h3>" . ucfirst(htmlspecialchars($k)) . "</h3>";
            if (is_array($v)) {
                // 📋 ESTADÍSTICAS EN LISTA (para arrays)
                echo "<ul style='list-style:none; padding:0; margin:0;'>";
                foreach ($v as $subk => $subv) {
                    echo "<li><strong>" . htmlspecialchars($subk) . ":</strong> " . htmlspecialchars((string)$subv) . "</li>";
                }
                echo "</ul>";
            } else {
                // 🔢 ESTADÍSTICAS NUMÉRICAS (para valores simples)
                echo "<span class='number'>" . htmlspecialchars((string)$v) . "</span>";
            }
            echo "</div>";
        }
        echo "</div>";
        break;

    // 📝 PÁGINA DE AUDITORÍA
    case 'auditoria':
        echo "<h1>📝 Historial de Acciones del Sistema</h1>";
        $todosLosEventos = [];
        
        // 🔄 RECOPILA eventos de auditoría de todos los materiales
        foreach ($biblioteca->getMaterials() as $material) {
            foreach ($material->obtenirHistorial() as $accio) {
                $todosLosEventos[] = [
                    'material' => $material->getTitol(),
                    'tipo' => $material->getTipus(),
                    'accio' => $accio['accio'],
                    'detalls' => $accio['detalls'],
                    'data' => $accio['data']
                ];
            }
        }

        // 📅 ORDENA eventos por fecha (más reciente primero)
        usort($todosLosEventos, function($a, $b) {
            return strcmp($b['data'], $a['data']);  // ⏰ Orden descendente
        });

        if ($todosLosEventos) {
            echo "<table class='table'>
                <thead>
                    <tr><th>Fecha</th><th>Material</th><th>Tipo</th><th>Acción</th><th>Detalles</th></tr>
                </thead>
                <tbody>";
            
            foreach ($todosLosEventos as $evento) {
                echo "<tr>
                    <td>{$evento['data']}</td>
                    <td>{$evento['material']}</td>
                    <td>{$evento['tipo']}</td>
                    <td><span class='badge info'>" . ucfirst($evento['accio']) . "</span></td>
                    <td>{$evento['detalls']}</td>
                </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No hay acciones registradas en el historial.</p>";
        }
        break;

    // ➕ PÁGINA DE CREACIÓN DE USUARIO
    case 'nou_usuari':
        echo "<h1>➕ Afegir Nou Usuari</h1>";
        
        // 📨 VERIFICA si es una petición POST (envío de formulario)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            // ✅ VALIDA que ambos campos estén completos
            if ($nom && $email) {
                try {
                    // 🧍 INTENTA crear el nuevo usuario
                    $nouUsuari = new Usuari($nom, $email);
                    $biblioteca->afegirUsuari($nouUsuari);
                    $_SESSION['biblioteca'] = $biblioteca;  // 💾 Actualiza sesión
                    $_SESSION['msg'] = "✅ Usuario creado con éxito";
                    header('Location: index.php?page=usuaris');  // 🔄 Redirige a lista
                    exit;
                } catch (Exception $e) {
                    // ❌ CAPTURA errores de validación (email inválido, etc.)
                    echo "<p class='alert alert-error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            } else {
                echo "<p class='alert alert-error'>Todos los campos son obligatorios.</p>";
            }
        }
        
        // 📝 FORMULARIO DE CREACIÓN DE USUARIO
        echo "
            <form method='POST' style='max-width: 500px;'>
                <div style='margin-bottom: 15px;'>
                    <label style='display: block; margin-bottom: 5px; font-weight: bold;'>Nom:</label>
                    <input type='text' name='nom' required style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>
                </div>
                <div style='margin-bottom: 15px;'>
                    <label style='display: block; margin-bottom: 5px; font-weight: bold;'>Email:</label>
                    <input type='email' name='email' required style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>
                </div>
                <button type='submit' class='btn btn-primary'>Crear Usuari</button>
                <a href='index.php?page=usuaris' class='btn btn-secondary'>Cancelar</a>
            </form>
        ";
        break;

    // ============================================================================
    // 🔧 DEMOSTRACIONES TÉCNICAS - CARACTERÍSTICAS AVANZADAS DE PHP
    // ============================================================================

    // 🎩 DEMOSTRACIÓN DE MÉTODOS MÁGICOS
    case 'demo_magic':
        echo "<h1>🎩 Demostración de Métodos Mágicos</h1>";
        
        echo "<h2>🔍 __get y __set</h2>";
        
        // 🧪 CLASE DE DEMOSTRACIÓN para métodos mágicos
        class DemoMagic {
            private $data = [];  // 🗄️ Array para almacenar propiedades dinámicas
            
            // 🔮 __get - Se ejecuta al acceder a propiedad inexistente
            public function __get($name) { 
                return $this->data[$name] ?? "❌ Propiedad '$name' no existe"; 
            }
            
            // 🔮 __set - Se ejecuta al asignar a propiedad inexistente  
            public function __set($name, $value) { 
                $this->data[$name] = "SET: $value"; 
            }
            
            // 🔮 __toString - Se ejecuta al convertir objeto a string
            public function __toString() { 
                return "📦 Instancia DemoMagic: " . json_encode($this->data); 
            }
            
            // 🔮 __call - Se ejecuta al llamar método inexistente
            public function __call($name, $args) { 
                return "🔮 Método '$name' llamado con argumentos: " . implode(", ", $args); 
            }
        }
        
        // 🧪 INSTANCIA y pruebas de la clase
        $obj = new DemoMagic();
        $obj->nom = "Test User";  // 🔥 Activa __set
        $obj->edat = 25;          // 🔥 Activa __set
        
        // 📊 MUESTRA resultados de las pruebas
        echo "<div class='list-group'>";
        echo "<div class='list-item'><strong>__set:</strong> obj->nom = 'Test User'</div>";
        echo "<div class='list-item'><strong>__set:</strong> obj->edat = 25</div>";
        echo "<div class='list-item'><strong>__get('nom'):</strong> " . htmlspecialchars($obj->nom) . "</div>";        // 🔥 Activa __get
        echo "<div class='list-item'><strong>__get('email'):</strong> " . htmlspecialchars($obj->email) . "</div>";    // 🔥 Propiedad inexistente
        echo "<div class='list-item'><strong>__toString:</strong> " . htmlspecialchars((string)$obj) . "</div>";       // 🔥 Activa __toString
        echo "<div class='list-item'><strong>__call:</strong> " . htmlspecialchars($obj->saludar("Hola", "Mundo")) . "</div>";  // 🔥 Activa __call
        echo "</div>";
        break;

    // 💾 DEMOSTRACIÓN DE SERIALIZACIÓN
    case 'demo_serialization':
        echo "<h1>💾 Demostración Serialización de Usuarios</h1>";
        
        try {
            // 🧪 CREA usuario de prueba con datos
            $user = new Usuari("DemoUser", "demo@example.com");
            $user->afegirPrestec($biblioteca->cercarPerId(1)); // ➕ Añade préstamo de ejemplo
            
            echo "<h2>📤 Serializando usuario...</h2>";
            $serialized = serialize($user);  // 💾 Convierte objeto a string
            echo "<div class='list-group'>";
            echo "<div class='list-item'><strong>Serializado:</strong> <code>" . htmlspecialchars(substr($serialized, 0, 100)) . "...</code></div>";
            
            echo "<h2>📥 Deserializando usuario...</h2>";
            $unserialized = unserialize($serialized);  // 🔄 Recupera objeto desde string
            echo "<div class='list-item'><strong>Nombre:</strong> " . htmlspecialchars($unserialized->nom) . "</div>";
            echo "<div class='list-item'><strong>Email:</strong> " . htmlspecialchars($unserialized->email) . "</div>";
            echo "<div class='list-item'><strong>Materiales Prestados:</strong> " . $unserialized->getNumeroMaterialsPrestat() . "</div>";
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<p class='alert alert-error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        break;

    // 🚨 DEMOSTRACIÓN DE EXCEPCIONES PERSONALIZADAS
    case 'demo_exceptions':
        echo "<h1>🚨 Pruebas de Excepciones Personalizadas</h1>";
        
        echo "<h2>🧪 Probando excepciones...</h2>";
        echo "<div class='list-group'>";
        
        // 🧪 PRUEBA MaterialNoDisponibleException
        try {
            throw new MaterialNoDisponibleException(999, "Material de prueba no disponible");
            echo "<div class='list-item'>❌ No se lanzó la excepción</div>";
        } catch (MaterialNoDisponibleException $e) {
            echo "<div class='list-item'><strong>MaterialNoDisponibleException:</strong> " . htmlspecialchars($e->getMessage()) . " (ID: " . $e->getMaterialId() . ")</div>";
        }
        
        // 🧪 PRUEBA EmailInvalidException
        try {
            throw new EmailInvalidException("email-invalido", "Email de prueba inválido");
            echo "<div class='list-item'>❌ No se lanzó la excepción</div>";
        } catch (EmailInvalidException $e) {
            echo "<div class='list-item'><strong>EmailInvalidException:</strong> " . htmlspecialchars($e->getMessage()) . " (Email: " . $e->getEmail() . ")</div>";
        }
        
        // 🧪 PRUEBA UsuariNoTrobatException
        try {
            throw new UsuariNoTrobatException("UsuarioInexistente", "Usuario de prueba no encontrado");
            echo "<div class='list-item'>❌ No se lanzó la excepción</div>";
        } catch (UsuariNoTrobatException $e) {
            echo "<div class='list-item'><strong>UsuariNoTrobatException:</strong> " . htmlspecialchars($e->getMessage()) . " (Usuario: " . $e->getNomUsuari() . ")</div>";
        }
        
        echo "</div>";
        break;

    // 🔧 DEMOSTRACIÓN DE AUTOLOADING PSR-4
    case 'demo_autoload':
        echo "<h1>🔧 Test de Autoloading PSR-4</h1>";
        
        echo "<h2>✅ Verificando carga de clases...</h2>";
        echo "<div class='list-group'>";
        
        // 📋 LISTA de clases principales del sistema
        $clases = ['Material', 'Llibre', 'DVD', 'Revista', 'Usuari', 'Prestec', 'Biblioteca'];
        $interfaces = ['Reservable'];
        $traits = ['Auditoria'];
        $exceptions = ['MaterialNoDisponibleException', 'EmailInvalidException', 'UsuariNoTrobatException', 'MaterialJaPrestatException'];
        
        // 🔍 VERIFICA carga de clases
        foreach ($clases as $clase) {
            if (class_exists($clase)) {
                echo "<div class='list-item'><span class='badge success'>✓</span> Clase: <strong>$clase</strong></div>";
            } else {
                echo "<div class='list-item'><span class='badge danger'>✗</span> Clase: <strong>$clase</strong> - NO CARGADA</div>";
            }
        }
        
        // 🔍 VERIFICA carga de interfaces
        foreach ($interfaces as $interface) {
            if (interface_exists($interface)) {
                echo "<div class='list-item'><span class='badge success'>✓</span> Interface: <strong>$interface</strong></div>";
            } else {
                echo "<div class='list-item'><span class='badge danger'>✗</span> Interface: <strong>$interface</strong> - NO CARGADA</div>";
            }
        }
        
        // 🔍 VERIFICA carga de traits
        foreach ($traits as $trait) {
            if (trait_exists($trait)) {
                echo "<div class='list-item'><span class='badge success'>✓</span> Trait: <strong>$trait</strong></div>";
            } else {
                echo "<div class='list-item'><span class='badge danger'>✗</span> Trait: <strong>$trait</strong> - NO CARGADO</div>";
            }
        }
        
        // 🔍 VERIFICA carga de excepciones personalizadas
        foreach ($exceptions as $exception) {
            if (class_exists($exception)) {
                echo "<div class='list-item'><span class='badge success'>✓</span> Excepción: <strong>$exception</strong></div>";
            } else {
                echo "<div class='list-item'><span class='badge danger'>✗</span> Excepción: <strong>$exception</strong> - NO CARGADA</div>";
            }
        }
        
        echo "</div>";
        
        // 🔍 INFORMACIÓN DEL SISTEMA PHP
        echo "<h2>🔍 Información del Sistema</h2>";
        echo "<div class='list-group'>";
        echo "<div class='list-item'><strong>PHP Version:</strong> " . PHP_VERSION . "</div>";
        echo "<div class='list-item'><strong>Extensions cargadas:</strong> " . implode(", ", get_loaded_extensions()) . "</div>";
        echo "</div>";
        break;

    // ❌ PÁGINA NO ENCONTRADA
    default:
        echo "<h1>Página no encontrada</h1>";
        echo "<p>La página solicitada no existe.</p>";
        break;
}
?>
</div>
</body>
</html>
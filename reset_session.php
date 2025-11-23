<?php
require_once 'autoload.php';
session_start();
// Elimina la biblioteca guardada en sesión (si la hubiera)
unset($_SESSION['biblioteca']);
// Opcional: destruir toda la sesión
// session_unset(); session_destroy();
echo "Sesión reseteada. <a href='index.php'>Volver al index</a>";
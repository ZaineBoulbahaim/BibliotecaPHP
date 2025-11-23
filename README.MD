📚 Sistema de Gestión de Biblioteca Digital
🎯 Descripción del Proyecto
Sistema completo de gestión de biblioteca desarrollado en PHP Orientado a Objetos que implementa todos los conceptos avanzados de POO. Permite gestionar materiales bibliográficos, usuarios, préstamos, reservas y auditoría.

🚀 Características Principales
📦 Gestión de Materiales
✅ Libros, DVDs y Revistas con propiedades específicas

✅ Sistema de préstamos con cálculo automático de multas

✅ Reservas inteligentes (solo libros y DVDs)

✅ Búsqueda avanzada por título, autor y ID

✅ Filtrado por tipo de material

👥 Gestión de Usuarios
✅ Registro y validación de usuarios con email

✅ Perfiles de usuario con historial de préstamos

✅ Sistema de préstamos personalizado por usuario

✅ Métodos mágicos para acceso seguro a propiedades

🏗️ Arquitectura del Sistema
📁 Estructura de Archivos
text
biblioteca/
├── classes/                  
│   ├── Material.php         
│   ├── Llibre.php           
│   ├── DVD.php              
│   ├── Revista.php          
│   ├── Usuari.php           
│   ├── Prestec.php          
│   └── Biblioteca.php       
├── interfaces/
│   └── Reservable.php       
├── traits/
│   └── Auditoria.php        
├── exceptions/              
│   ├── MaterialNoDisponibleException.php
│   ├── MaterialJaPrestatException.php
│   ├── UsuariNoTrobatException.php
│   └── EmailInvalidException.php
├── autoload.php            
├── index.php               
└── reset_session.php       
🎯 Diagrama de Clases
text
Material (Abstract) ↗ use Auditoria
    ├── Llibre ↗ implements Reservable
    ├── DVD ↗ implements Reservable  
    └── Revista

Usuari ↗ __get, __set, __toString
Prestec ↗ Gestión de préstamos
Biblioteca ↗ __call para métodos dinámicos

Reservable (Interface)
Auditoria (Trait)
🛠️ Tecnologías Implementadas
🔧 Características PHP 8
✅ Typed Properties: private int $id

✅ Union Types: string|int

✅ Nullable Types: ?string $usuariReserva = null

✅ Match Expressions

✅ Constructor Property Promotion

🎩 Conceptos POO
✅ Clases Abstractas: Material como base

✅ Herencia: Llibre, DVD, Revista ← Material

✅ Interfaces: Reservable para comportamiento específico

✅ Traits: Auditoria para funcionalidad transversal

✅ Polimorfismo: Mismo método, diferentes implementaciones

✅ Métodos Mágicos: __get, __set, __toString, __call

🚀 Instalación Rápida
📋 Requisitos
PHP 8.0+

Servidor web (Apache, Nginx, o PHP built-in server)

Sesiones PHP habilitadas

⚡ Configuración
bash
# Clonar o descargar el proyecto
cd biblioteca

# Usar PHP built-in server
php -S localhost:8000

# Acceder en navegador
http://localhost:8000
🎮 Guía de Uso Rápida
📚 Páginas Principales
Página	URL	Descripción
🏠 Dashboard	index.php	Estadísticas del sistema
📚 Materiales	?page=materials	Todos los materiales
📖 Libros	?page=materials&type=llibre	Solo libros
💿 DVDs	?page=materials&type=dvd	Solo DVDs
📰 Revistas	?page=materials&type=revista	Solo revistas
👥 Usuarios	?page=usuaris	Lista de usuarios
🔄 Préstamos	?page=prestecs	Préstamos activos
📊 Stats	?page=estadistiques	Estadísticas
📝 Auditoría	?page=auditoria	Historial de acciones
👤 Gestión de Usuarios
Ver perfil: ?page=usuari&id=Alice

Nuevo usuario: ?page=nou_usuari

🔧 Demostraciones Técnicas
Métodos mágicos: ?page=demo_magic

Serialización: ?page=demo_serialization

Excepciones: ?page=demo_exceptions

Autoloading: ?page=demo_autoload
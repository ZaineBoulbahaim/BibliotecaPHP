<?php

class Biblioteca {
    // 🗄️ PROPIEDADES PRIVADAS con typed properties (PHP 8)
    private array $materials = [];       // 📚 Colección de todos los materiales
    private array $usuaris = [];         // 👥 Lista de usuarios registrados
    private array $prestecsActius = [];  // 🔄 Préstamos actualmente activos
    private string $nom;                 // 🏷️ Nombre de la biblioteca

    // 🏗️ CONSTRUCTOR con promoted properties (podría ser)
    public function __construct(string $nom) {
        $this->nom = $nom;
    }

    // ➕ AÑADIR MATERIAL - Acepta cualquier clase que herede de Material
    public function afegirMaterial(Material $material): void {
        $this->materials[] = $material;  // ✅ Polimorfismo en acción
    }

    // 🗑️ ELIMINAR MATERIAL por ID
    public function eliminarMaterial(int $id): bool {
        foreach ($this->materials as $key => $material) {
            if ($material->getId() == $id) {
                unset($this->materials[$key]);
                $this->materials = array_values($this->materials);  // 🔄 Reindexa array
                return true;
            }
        }
        return false;  // ❌ Material no encontrado
    }

    // 🔍 BÚSQUEDA POR TÍTULO (case-insensitive)
    public function cercaPerTitol(string $titol): array {
        $resultat = [];
        foreach ($this->materials as $material) {
            // 📝 stripos() busca sin distinguir mayúsculas/minúsculas
            if (stripos($material->getTitol(), $titol) !== false) {
                $resultat[] = $material;
            }
        }
        return $resultat;
    }

    // 👤 BÚSQUEDA POR AUTOR (case-insensitive)
    public function cercaPerAutor(string $autor): array {
        $resultat = [];
        foreach ($this->materials as $material) {
            if (stripos($material->getAutor(), $autor) !== false) {
                $resultat[] = $material;
            }
        }
        return $resultat;
    }

    // 🔎 BÚSQUEDA POR ID EXACTO
    public function cercarPerId(int $id): ?Material {
        foreach ($this->materials as $material) {
            if ($material->getId() === $id) {
                return $material;  // ✅ Encontrado
            }
        }
        return null;  // ❌ No encontrado (nullable return type)
    }

    // 📋 LISTAR MATERIALES DISPONIBLES
    public function llistarDisponibles(): array {
        $resultat = [];
        foreach ($this->materials as $material) {
            if ($material->isDisponible()) {
                $resultat[] = $material;
            }
        }
        return $resultat;
    }

    // 📋 LISTAR MATERIALES PRESTADOS
    public function llistarPrestat(): array {
        $resultat = [];
        foreach ($this->materials as $material) {
            if (!$material->isDisponible()) {
                $resultat[] = $material;
            }
        }
        return $resultat;
    }

    // 🏷️ FILTRAR POR TIPO DE MATERIAL
    public function llistarPerTipus(string $tipus): array {
        $resultat = [];
        foreach ($this->materials as $material) {
            if ($material->getTipus() === $tipus){
                $resultat[] = $material;
            }
        }
        return $resultat;
    }

    // ============================================================================
    // 🔄 GESTIÓN DE PRÉSTAMOS
    // ============================================================================

    // 📥 PRESTAR MATERIAL con control de errores
    public function prestarMaterial(int $materialId, Usuari $usuari): bool {
        $material = $this->cercarPerId($materialId);

        // 🚨 EXCEPCIONES PERSONALIZADAS para control de errores
        if ($material === null) {
            throw new MaterialNoDisponibleException($materialId, "Material no encontrado");
        }

        if (!$material->isDisponible()) {
            throw new MaterialJaPrestatException($materialId, "Material ya prestado");
        }

        // 📝 CREAR registro de préstamo
        $prestec = new Prestec($material, $usuari);
        $this->prestecsActius[] = $prestec;

        return $material->prestar($usuari);  // ✅ Delegar al material
    }
    
    // 📤 DEVOLVER MATERIAL
    public function retornarMaterial(int $materialId): bool {
        $material = $this->cercarPerId($materialId);
        
        if ($material === null) {
            return false;
        }

        // 🔍 BUSCAR y eliminar préstamo activo
        foreach ($this->prestecsActius as $key => $prestec) {
            if ($prestec->getMaterial()->getId() === $materialId) {
                $prestec->retornar();  // ✅ Ejecutar lógica de retorno
                unset($this->prestecsActius[$key]);
                $this->prestecsActius = array_values($this->prestecsActius);
                return true;
            }
        }

        return $material->retornar();  // ✅ Fallback al método del material
    }

    // ============================================================================
    // 👥 GESTIÓN DE USUARIOS
    // ============================================================================

    // ➕ AÑADIR USUARIO
    public function afegirUsuari(Usuari $usuari): void {
        $this->usuaris[] = $usuari;
    }

    // 🔍 BUSCAR USUARIO por nombre (case-insensitive)
    public function cercarUsuari(string $nom): ?Usuari {
        foreach ($this->usuaris as $usuari) {
            // 🔄 strcasecmp() compara sin distinguir mayúsculas/minúsculas
            if (strcasecmp($usuari->nom, $nom) === 0) {
                return $usuari;
            }
        }
        return null;
    }

    // 📊 ESTADÍSTICAS DEL SISTEMA en tiempo real
    public function obtenirEstadistiques(): array {
        $total = count($this->materials);
        $disponibles = 0;
        $prestats = 0;
        $perTipus = [
            'Llibre' => 0,
            'Revista' => 0,
            'DVD' => 0
        ];

        // 🔄 CONTAR materiales por estado y tipo
        foreach ($this->materials as $material) {
            if ($material->isDisponible()) {
                $disponibles++;
            } else {
                $prestats++;
            }

            $tipus = $material->getTipus();
            if (isset($perTipus[$tipus])) {
                $perTipus[$tipus]++;
            } else {
                $perTipus[$tipus] = 1;  // 🔧 Para tipos no predefinidos
            }
        }

        return [
            'total' => $total,
            'disponibles' => $disponibles,
            'prestats' => $prestats,
            'perTipus' => $perTipus,
            'usuaris' => count($this->usuaris),
            'prestecs_actius' => count($this->prestecsActius)
        ];
    }

    // ============================================================================
    // 🔧 GETTERS PÚBLICOS
    // ============================================================================

    public function getMaterials(): array {
        return $this->materials;
    }

    public function getUsuaris(): array {
        return $this->usuaris;
    }

    public function getPrestecsActius(): array {
        return $this->prestecsActius;
    }

    public function getNom(): string {
        return $this->nom;
    }

    // ============================================================================
    // 🎯 MÉTODOS ESPECÍFICOS POR TIPO (alternativa a __call)
    // ============================================================================

    public function getLlibres(): array {
        return $this->llistarPerTipus('Llibre');  // ✅ Reutiliza método existente
    }

    public function getDVDs(): array {
        return $this->llistarPerTipus('DVD');
    }

    public function getRevistes(): array {
        return $this->llistarPerTipus('Revista');
    }

    // ============================================================================
    // 🔮 MÉTODO MÁGICO __call para métodos dinámicos
    // ============================================================================

    public function __call($nomMètode, $arguments) {
        // 🎯 Para métodos que empiezan con 'get' (ej: getComics, getPelículas)
        if (strpos($nomMètode, 'get') === 0) {
            $tipus = substr($nomMètode, 3);  // 📝 Quita "get" del nombre
            $tipus = ucfirst(strtolower($tipus));  // 🏷️ Normaliza formato
            
            // 🛡️ Solo permitir tipos conocidos por seguridad
            $tiposPermitidos = ['Llibre', 'DVD', 'Revista'];
            if (in_array($tipus, $tiposPermitidos)) {
                return $this->llistarPerTipus($tipus);
            }
        }

        // 🚨 Lanzar excepción si el método no existe
        throw new BadMethodCallException("Mètode $nomMètode no existeix.");
    }
}
?>
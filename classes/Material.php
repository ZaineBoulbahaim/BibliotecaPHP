<?php

// 🏛️ CLASE ABSTRACTA - No se puede instanciar directamente
abstract class Material {
    use Auditoria;  // 🔄 Todas las materiales tienen auditoría

    // 📊 PROPIEDADES PROTEGIDAS (accesibles por clases hijas)
    protected int $id;
    protected string $titol;
    protected string $autor;
    protected int $anyPublicacio;
    protected bool $disponible = true;  // ✅ Estado por defecto

    // 🔄 PROPIEDADES DE PRÉSTAMO
    protected ?Usuari $usuariPrestec = null;    // 👤 Usuario actual
    protected ?DateTime $dataPrestec = null;    // 📅 Fecha de préstamo

    // 🏗️ CONSTRUCTOR con parámetros básicos
    public function __construct(int $id, string $titol, string $autor, int $anyPublicacio) {
        $this->id = $id;
        $this->titol = $titol;
        $this->autor = $autor;
        $this->anyPublicacio = $anyPublicacio;
    }

    // ============================================================================
    // 📄 MÉTODOS ABSTRACTOS - DEBEN ser implementados por clases hijas
    // ============================================================================

    abstract public function calcularMulta(int $diesRetard): float;  // 💰 Cálculo de multa
    abstract public function getTipus(): string;                     // 🏷️ Tipo de material

    // ============================================================================
    // 🔄 MÉTODOS DE GESTIÓN DE PRÉSTAMOS
    // ============================================================================

    // 📥 PRESTAR material
    public function prestar(Usuari $usuari): bool {
        if (!$this->disponible) {
            return false;  // ❌ Ya está prestado
        }

        // ✅ Actualizar estado
        $this->usuariPrestec = $usuari;
        $this->dataPrestec = new DateTime();  // ⏰ Fecha actual
        $this->disponible = false;

        $this->registrarAccio('prestat', "Usuari: {$usuari->nom}");  // 📝 Auditoría
        return true;
    }

    // 📤 DEVOLVER material
    public function retornar(): bool {
        if ($this->disponible) {
            return false;  // ❌ Ya estaba disponible
        }

        $nomUsuari = $this->usuariPrestec ? $this->usuariPrestec->nom : "Cap usuari";
        
        // ✅ Restablecer estado
        $this->disponible = true;
        $this->usuariPrestec = null;
        $this->dataPrestec = null;

        $this->registrarAccio('retornat', "Usuari: $nomUsuari");  // 📝 Auditoría
        return true;
    }

    // ============================================================================
    // 🔍 GETTERS DE INFORMACIÓN DE PRÉSTAMO
    // ============================================================================

    public function getUsuariPrestec(): ?Usuari {
        return $this->usuariPrestec;  // 📌 Puede ser null
    }

    public function getDataPrestec(): ?DateTime {
        return $this->dataPrestec;    // 📌 Puede ser null
    }

    // ============================================================================
    // 🔧 GETTERS GENERALES (acceso a propiedades protegidas)
    // ============================================================================

    public function getId(): int {
        return $this->id;
    }

    public function getTitol(): string {
        return $this->titol;
    }

    public function getAutor(): string {
        return $this->autor;
    }

    public function getAnyPublicacio(): int {
        return $this->anyPublicacio;
    }

    public function isDisponible(): bool {
        return $this->disponible;  // ✅ Método con naming boolean
    }

    // ============================================================================
    // 🔮 MÉTODO MÁGICO __toString
    // ============================================================================

    public function __toString(): string {
        $estat = $this->disponible ? "Disponible" : "No disponible";
        return "{$this->getTipus()}: {$this->titol} de {$this->autor} ($this->anyPublicacio) - $estat";
    }
}
?>
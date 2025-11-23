<?php

// 📰 Revista hereda de Material pero NO implementa Reservable
class Revista extends Material {
    use Auditoria;  // 🔄 Solo auditoría, no reservas

    // 🏷️ PROPIEDAD ESPECÍFICA
    private int $numeroEdicio;  // 🔢 Número de edición

    // 🏗️ CONSTRUCTOR
    public function __construct(int $id, string $titol, string $autor, int $anyPublicacio, int $numeroEdicio) {
        parent::__construct($id, $titol, $autor, $anyPublicacio);
        $this->setNumeroEdicio($numeroEdicio);  // 🛡️ Validación
    }

    // ============================================================================
    // 📄 IMPLEMENTACIÓN DE MÉTODOS ABSTRACTOS
    // ============================================================================

    // 💰 MULTA específica para revistas (más baja)
    public function calcularMulta(int $diesRetard): float {
        return 0.25 * $diesRetard;  // 💵 0.25€ por día de retraso
    }

    // 🏷️ TIPO específico
    public function getTipus(): string {
        return "Revista";  // ✅ Debe ser exacto
    }

    // ============================================================================
    // 🔧 GETTERS Y SETTERS ESPECÍFICOS
    // ============================================================================

    public function getNumeroEdicio(): int {
        return $this->numeroEdicio;
    }

    // 🛡️ SETTER CON VALIDACIÓN
    public function setNumeroEdicio(int $numero): void {
        if ($numero > 0) {
            $this->numeroEdicio = $numero;
        } else {
            throw new InvalidArgumentException("El número de edición debe ser mayor a 0.");
        }
    }

    // ============================================================================
    // 🔮 MÉTODO MÁGICO __toString
    // ============================================================================

    public function __toString(): string {
        $base = parent::__toString();
        return "$base - Edición nº {$this->numeroEdicio}";  // 📝 Incluye número edición
    }
}
?>
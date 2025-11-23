<?php

// 📚 Llibre hereda de Material e IMPLEMENTA Reservable
class Llibre extends Material implements Reservable {
    use Auditoria;  // 🔄 Trait para auditoría

    // 📄 PROPIEDADES ESPECÍFICAS
    private int $numeroPagines;           // 🔢 Número de páginas
    private ?string $usuariReserva = null; // 👤 Usuario que reservó

    // 🏗️ CONSTRUCTOR
    public function __construct(int $id, string $titol, string $autor, int $anyPublicacio, int $numeroPagines) {
        parent::__construct($id, $titol, $autor, $anyPublicacio);
        $this->setNumeroPagines($numeroPagines);  // 🛡️ Validación en setter
    }

    // ============================================================================
    // 📅 IMPLEMENTACIÓN DE RESERVABLE (igual que DVD pero específico para libros)
    // ============================================================================

    public function reservar(string $nomUsuari): bool {
        if ($this->disponible) {
            $this->usuariReserva = $nomUsuari;
            $this->registrarAccio('reservat', "Usuari: $nomUsuari");
            return true;
        }
        return false;
    }
    
    public function cancelarReserva(): bool {
        if ($this->usuariReserva !== null) {
            $this->registrarAccio('reserva_cancelada', "Usuari: $this->usuariReserva");
            $this->usuariReserva = null;
            return true;
        }
        return false;
    }

    public function estaReservat(): bool {
        return $this->usuariReserva !== null;
    }

    public function getUsuariReserva(): ?string {
        return $this->usuariReserva;
    }

    // ============================================================================
    // 📄 IMPLEMENTACIÓN DE MÉTODOS ABSTRACTOS
    // ============================================================================

    // 💰 MULTA específica para libros
    public function calcularMulta(int $diesRetard): float {
        return 0.50 * $diesRetard;  // 💵 0.50€ por día de retraso
    }

    // 🏷️ TIPO específico
    public function getTipus(): string {
        return "Llibre";  // ✅ Debe ser exacto
    }

    // ============================================================================
    // 🔧 GETTERS Y SETTERS ESPECÍFICOS
    // ============================================================================

    public function getNumeroPagines(): int {
        return $this->numeroPagines;
    }

    // 🛡️ SETTER CON VALIDACIÓN
    public function setNumeroPagines(int $pagines): void {
        if ($pagines > 0) {
            $this->numeroPagines = $pagines;
        } else {
            throw new InvalidArgumentException("El número de páginas debe ser mayor que 0.");
        }
    }

    // ============================================================================
    // 🔮 MÉTODO MÁGICO __toString
    // ============================================================================

    public function __toString(): string {
        $base = parent::__toString();
        $reserva = $this->estaReservat() ? " (Reservado por: {$this->usuariReserva})" : "";
        return "$base - {$this->numeroPagines} páginas$reserva";  // 📝 Incluye páginas
    }
}
?>
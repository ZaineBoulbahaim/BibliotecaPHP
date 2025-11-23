<?php

// 🎬 DVD hereda de Material e IMPLEMENTA Reservable
class DVD extends Material implements Reservable {
    use Auditoria;  // 🔄 Incluye funcionalidad de auditoría

    // 🕒 PROPIEDADES ESPECÍFICAS
    private int $duracio;  // ⏱️ Duración en minutos
    private ?string $usuariReserva = null;  // 👤 Usuario que reservó (nullable)

    // 🏗️ CONSTRUCTOR que llama al padre y añade propiedades específicas
    public function __construct(int $id, string $titol, string $autor, int $anyPublicacio, int $duracio) {
        parent::__construct($id, $titol, $autor, $anyPublicacio);  // ✅ Llama constructor padre
        $this->setDuracio($duracio);  // 🛡️ Usa setter para validación
    }

    // ============================================================================
    // 📅 IMPLEMENTACIÓN DE LA INTERFACE RESERVABLE
    // ============================================================================

    // 📌 RESERVAR DVD
    public function reservar(string $nomUsuari): bool {
        if ($this->disponible) {
            $this->usuariReserva = $nomUsuari;
            $this->registrarAccio('reservat', "Usuari: $nomUsuari");  // 📝 Auditoría
            return true;
        }
        return false;  // ❌ No disponible
    }

    // ❌ CANCELAR RESERVA
    public function cancelarReserva(): bool {
        if ($this->usuariReserva !== null) {
            $this->registrarAccio('reserva_cancelada', "Usuari: $this->usuariReserva");
            $this->usuariReserva = null;  // 🗑️ Limpiar reserva
            return true;
        }
        return false;  // ❌ No había reserva
    }

    // 🔍 VERIFICAR SI ESTÁ RESERVADO
    public function estaReservat(): bool {
        return $this->usuariReserva !== null;  // ✅ Simple verificación
    }

    // 👤 OBTENER USUARIO DE RESERVA
    public function getUsuariReserva(): ?string {
        return $this->usuariReserva;  // 📌 Puede ser null
    }

    // ============================================================================
    // 📄 IMPLEMENTACIÓN DE MÉTODOS ABSTRACTOS DE MATERIAL
    // ============================================================================

    // 💰 CÁLCULO DE MULTA específica para DVDs
    public function calcularMulta(int $diesRetard): float {
        return 1.00 * $diesRetard;  // 💵 1€ por día de retraso
    }

    // 🏷️ TIPO DE MATERIAL
    public function getTipus(): string {
        return "DVD";  // ✅ Debe coincidir exactamente
    }

    // ============================================================================
    // 🔧 GETTERS Y SETTERS ESPECÍFICOS
    // ============================================================================

    public function getDuracio(): int {
        return $this->duracio;
    }

    // 🛡️ SETTER CON VALIDACIÓN
    public function setDuracio(int $minuts): void {
        if ($minuts > 0) {
            $this->duracio = $minuts;
        } else {
            throw new InvalidArgumentException("La duración debe ser mayor a 0 minutos.");
        }
    }

    // 🕒 FORMATEAR DURACIÓN para mostrar
    public function getDuracioFormatada(): string {
        $hores = intdiv($this->duracio, 60);  // 🕐 División entera para horas
        $minuts = $this->duracio % 60;        // ⏱️ Resto para minutos
        return "{$hores}h {$minuts}min";      // 📝 Formato legible
    }

    // ============================================================================
    // 🔮 MÉTODO MÁGICO __toString
    // ============================================================================

    public function __toString(): string {
        $base = parent::__toString();  // ✅ Reutiliza toString del padre
        $reserva = $this->estaReservat() ? " (Reservado por: {$this->usuariReserva})" : "";
        return "$base - {$this->getDuracioFormatada()}$reserva";  // 📝 Info completa
    }
}
?>
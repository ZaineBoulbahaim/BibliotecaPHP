<?php

class Prestec {
    // 📊 PROPIEDADES PRIVADAS
    private Material $material;           // 📚 Material prestado
    private Usuari $usuari;               // 👤 Usuario que tomó prestado
    private DateTime $dataPrestec;        // 📅 Fecha de préstamo
    private ?DateTime $dataRetorn = null; // 📆 Fecha de devolución (nullable)
    private int $diesLimitPrestec;        // ⏰ Límite de días

    // 🏗️ CONSTRUCTOR con valor por defecto
    public function __construct(Material $material, Usuari $usuari, int $diesLimit = 14) {
        $this->material = $material;
        $this->usuari = $usuari;
        $this->diesLimitPrestec = $diesLimit;
        $this->dataPrestec = new DateTime();  // ⏰ Fecha actual automática

        $this->usuari->afegirPrestec($material);  // ✅ Actualizar usuario
    }

    // ============================================================================
    // 🔄 MÉTODOS DE GESTIÓN
    // ============================================================================

    // 📤 PROCESAR DEVOLUCIÓN
    public function retornar(): void {
        $this->dataRetorn = new DateTime();  // ⏰ Marcar fecha de retorno
        $this->usuari->eliminarPrestec($this->material->getId());  // ✅ Actualizar usuario
        $this->material->retornar();  // ✅ Actualizar material
    }

    // 📅 CALCULAR DÍAS DE RETRASO
    public function calcularDiesRetard(): int {
        $dataReferencia = $this->dataRetorn ?? new DateTime();  // ⏰ Usar fecha actual si no devuelto
        $diff = $this->dataPrestec->diff($dataReferencia);      // 📊 Diferencia de fechas
        $diesPassats = (int)$diff->format('%a');                // 🔢 Días transcurridos
        $diesRetard = $diesPassats - $this->diesLimitPrestec;   // 📈 Cálculo retraso
        return max(0, $diesRetard);  // 🛡️ No negativo
    }

    // 💰 CALCULAR MULTA delegando al material
    public function calcularMulta(): float {
        $diesRetard = $this->calcularDiesRetard();
        return $this->material->calcularMulta($diesRetard);  // ✅ Polimorfismo
    }

    // ⚠️ VERIFICAR SI ESTÁ VENCIDO
    public function estaVencut(): bool {
        return $this->calcularDiesRetard() > 0;  // ✅ Simple verificación
    }

    // 📋 CALCULAR DÍAS PENDIENTES
    public function getDiesPendents(): int {
        $now = new DateTime();
        $diff = $this->dataPrestec->diff($now);
        $diesPassats = (int)$diff->format('%a');
        return $this->diesLimitPrestec - $diesPassats;  // 🔢 Días que quedan
    }

    // ============================================================================
    // 🔍 GETTERS PÚBLICOS
    // ============================================================================

    public function getMaterial(): Material {
        return $this->material;
    }

    public function getUsuari(): Usuari {
        return $this->usuari;
    }

    public function getDataPrestec(): DateTime {
        return $this->dataPrestec;
    }

    public function getDataRetorn(): ?DateTime {
        return $this->dataRetorn;  // 📌 Puede ser null
    }

    public function getDiesLimitPrestec(): int {
        return $this->diesLimitPrestec;
    }
}
?>
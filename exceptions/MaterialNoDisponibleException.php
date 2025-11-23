<?php
// Excepción para cuando un material no está disponible para préstamo
class MaterialNoDisponibleException extends Exception {
    private int $materialId; // ID del material que generó la excepción

    // Constructor: acepta ID del material y mensaje opcional
    public function __construct(int $materialId, string $message = "Material no disponible") {
        parent::__construct($message);
        $this->materialId = $materialId;
    }

    // Getter del ID del material
    public function getMaterialId(): int {
        return $this->materialId;
    }

    // Mensaje personalizado al convertir a string
    public function __toString(): string {
        return "MaterialNoDisponibleException: ID del material {$this->materialId} - {$this->getMessage()}";
    }
}
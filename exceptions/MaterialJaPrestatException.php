<?php
// Excepción para cuando un material ya está prestado
class MaterialJaPrestatException extends Exception {
    private int $materialId;

    public function __construct(int $materialId, string $message = "Material ya prestado") {
        parent::__construct($message);
        $this->materialId = $materialId;
    }

    public function getMaterialId(): int {
        return $this->materialId;
    }

    public function __toString(): string {
        return "MaterialJaPrestatException: ID del material {$this->materialId} - {$this->getMessage()}";
    }
}

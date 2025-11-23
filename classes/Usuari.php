<?php

class Usuari {
    // 📊 PROPIEDADES PRIVADAS
    private string $nom;
    private string $email;
    private array $materialsPrestat = [];  // 📚 Lista de materiales prestados
    private DateTime $dataRegistre;        // 📅 Fecha de registro

    // 🏗️ CONSTRUCTOR con validación
    public function __construct(string $nom, string $email) {
        $this->nom = $nom;

        // 🛡️ VALIDAR email antes de asignar
        if (!$this->validarEmail($email)) {
            throw new EmailInvalidException($email, "Email no válido.");
        }
        $this->email = $email;
        $this->dataRegistre = new DateTime();  // ⏰ Fecha actual automática
    }

    // ============================================================================
    // 🔮 MÉTODOS MÁGICOS
    // ============================================================================

    // 🔍 __get - Acceso lectura a propiedades privadas
    public function __get(string $propietat) {
        if (property_exists($this, $propietat)) {
            return $this->$propietat;  // ✅ Permitir lectura
        }
        throw new Exception("Propiedad $propietat no existe.");  // ❌ Propiedad inexistente
    }

    // ✏️ __set - Control escritura de propiedades
    public function __set(string $propietat, $valor): void {
        if ($propietat === 'email') {
            // 🛡️ Solo permitir cambiar email con validación
            if (!$this->validarEmail($valor)) {
                throw new EmailInvalidException($valor, "Email no válido.");
            }
            $this->$propietat = $valor;
        } else {
            throw new Exception("No se puede modificar la propiedad $propietat.");  // ❌ Bloquear otras
        }
    }

    // 💾 __sleep - Especificar qué propiedades serializar
    public function __sleep(): array {
        return ['nom', 'email', 'materialsPrestat', 'dataRegistre'];  // ✅ Propiedades a serializar
    }

    // 🔄 __wakeup - Acciones después de deserializar
    public function __wakeup(): void {
        // 🔧 Podría usarse para regenerar propiedades no serializables
    }

    // 📝 __toString - Representación en string
    public function __toString(): string {
        return "Usuari: $this->nom, Email: $this->email, Materials prestats: " . count($this->materialsPrestat);
    }

    // ============================================================================
    // 📚 GESTIÓN DE PRÉSTAMOS DEL USUARIO
    // ============================================================================

    // ➕ AÑADIR material a la lista de prestados
    public function afegirPrestec(Material $material): void {
        $this->materialsPrestat[] = $material;  // ✅ Añadir al array
    }

    // 🗑️ ELIMINAR material de la lista por ID
    public function eliminarPrestec(int $materialId): void {
        foreach ($this->materialsPrestat as $key => $material) {
            if ($material->getId() === $materialId) {
                unset($this->materialsPrestat[$key]);
                $this->materialsPrestat = array_values($this->materialsPrestat);  // 🔄 Reindexar
                break;  // ⏹️ Salir después de encontrar
            }
        }
    }

    // 📋 OBTENER lista de materiales prestados
    public function getMaterialsPrestat(): array {
        return $this->materialsPrestat;
    }

    // 🔢 CONTAR materiales prestados
    public function getNumeroMaterialsPrestat(): int {
        return count($this->materialsPrestat);  // ✅ Simple conteo
    }

    // ============================================================================
    // 🛡️ MÉTODO PRIVADO DE VALIDACIÓN
    // ============================================================================

    private function validarEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;  // ✅ Validación PHP
    }
}
?>
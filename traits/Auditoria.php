<?php
trait Auditoria {
    private array $historial = [];

    // Método para registrar una acción
    public function registrarAccio(string $accio, string $detalls = ''): void {
        $this->historial[] = [
            'accio' => $accio,
            'detalls' => $detalls,
            'data' => date('Y-m-d H:i:s')
        ];
    }

    // Método original
    public function obtenirHistorial(): array {
        return $this->historial;
    }

    // Alias para compatibilidad con index.php y Material.php
    public function getHistorial(): array {
        return $this->obtenirHistorial();
    }

    // Obtener última acción
    public function obtenirUltimaAccio(): ?array {
        return empty($this->historial) ? null : $this->historial[count($this->historial) - 1];
    }

    // Limpiar historial
    public function netajarHistorial(): void {
        $this->historial = [];
    }
}

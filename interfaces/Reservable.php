<?php

// Declaramos una interface
interface Reservable {
    // Declaramos los métodos
    public function reservar(string $nomUsuari): bool;
    public function cancelarReserva(): bool;
    public function estaReservat(): bool;
    public function getUsuariReserva(): ?string;
}
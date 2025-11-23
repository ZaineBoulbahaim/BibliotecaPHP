<?php
// Excepción para cuando un usuario no se encuentra
class UsuariNoTrobatException extends Exception {
    private string $nomUsuari;

    public function __construct(string $nomUsuari, string $message = "Usuario no encontrado") {
        parent::__construct($message);
        $this->nomUsuari = $nomUsuari;
    }

    public function getNomUsuari(): string {
        return $this->nomUsuari;
    }

    public function __toString(): string {
        return "UsuariNoTrobatException: Usuario '{$this->nomUsuari}' - {$this->getMessage()}";
    }
}

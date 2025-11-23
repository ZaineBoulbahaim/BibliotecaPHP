<?php
// Excepción para cuando un email no es válido
class EmailInvalidException extends Exception {
    private string $email;

    public function __construct(string $email, string $message = "Email inválido") {
        parent::__construct($message);
        $this->email = $email;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function __toString(): string {
        return "EmailInvalidException: Email '{$this->email}' - {$this->getMessage()}";
    }
}

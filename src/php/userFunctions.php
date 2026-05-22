<?php

function isCliente() {
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'cliente';
}

function isAdmin() {
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin';
}

function getTipoUsuario() {
    return $_SESSION['tipo'] ?? 'visitante';
}

function requiresTipo($tipoPermitido) {
    if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== $tipoPermitido) {
        header("Location: ../../../loginForm.html");
        exit;
    }
}
?>
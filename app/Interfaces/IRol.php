<?php
// app/Interfaces/IRol.php
namespace App\Interfaces;

interface IRol {
    public function getNombreRol(): string;
    public function puedeGestionarProyectos(): bool;
    public function puedeGestionarUsuarios(): bool;
    // ... otros permisos genéricos
}
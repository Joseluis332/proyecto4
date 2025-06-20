<?php
// app/Roles/AdminRol.php
namespace App\Roles;
use App\Interfaces\IRol;

class AdminRol implements IRol {
    public function getNombreRol(): string { return 'Administrador'; }
    public function puedeGestionarProyectos(): bool { return true; }
    public function puedeGestionarUsuarios(): bool { return true; }
}
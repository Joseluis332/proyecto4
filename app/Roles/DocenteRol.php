<?php
// app/Roles/DocenteRol.php
namespace App\Roles;
use App\Interfaces\IRol;

class DocenteRol implements IRol {
    public function getNombreRol(): string { return 'Docente'; }
    public function puedeGestionarProyectos(): bool { return true; } // Los docentes gestionan sus proyectos
    public function puedeGestionarUsuarios(): bool { return false; }
}
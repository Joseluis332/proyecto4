<?php

namespace App\Core;

interface MiddlewareInterface {
    /**
     * Procesa la solicitud antes de que llegue al controlador.
     * @return bool Retorna true si la solicitud debe continuar al controlador, false en caso contrario.
     */
    public function handle(): bool;
}
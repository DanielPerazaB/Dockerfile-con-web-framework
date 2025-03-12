<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/alumnos', function () {
    return response()->json([
        ['nombre' => 'Juan Pérez', 'matricula' => 'A001'],
        ['nombre' => 'María López', 'matricula' => 'A002'],
        ['nombre' => 'Carlos Ramírez', 'matricula' => 'A003'],
        ['nombre' => 'Ana Torres', 'matricula' => 'A004'],
    ]);
});
 
Route::get('/profesores', function () {
    return response()->json([
        ['nombre' => 'Pedro Gómez', 'numeroEmpleado' => 'P001'],
        ['nombre' => 'Luis Fernández', 'numeroEmpleado' => 'P002'],
        ['nombre' => 'Laura Sánchez', 'numeroEmpleado' => 'P003'],
        ['nombre' => 'Mónica Duarte', 'numeroEmpleado' => 'P004'],
    ]);
});


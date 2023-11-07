<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('registrarPokemon', [PokemonController::class, 'registrarPokemon']);
Route::post('actualizarPoke',[PokemonController::class, 'actualizarPokemon']);
Route::get('listarPoke',[PokemonController::class, 'listarPokemones']);
Route::get('eliminarPoke',[PokemonController::class, 'EliminarPokemon']);
Route::get('cargarPokes',[PokemonController::class, 'CargarPokemon']);
Route::get('listarPokesPorRegion',[PokemonController::class, 'listarPokemonesPorRegion']);
Route::get('listarPokesPorTipo',[PokemonController::class, 'listarPokemonesPorTipo']);
Route::get('listarPokePorNombre', [PokemonController::class, 'listarPokemonPorNombre']);
Route::get('listarPokePorPokedex', [PokemonController::class, 'listarPokemonPorPokedex']);
Route::get('listarPokesOpciones', [PokemonController::class, 'listarPokemonesOpciones']);






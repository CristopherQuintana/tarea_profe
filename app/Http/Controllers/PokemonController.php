<?php

namespace App\Http\Controllers;

use App\Http\Requests\{ListarPokeRequest,PokemonRequest,ListarPokePorRegionRequest,ListarPokePorTipoRequest,ListarPokePorNombreRequest,ListarPokePorPokedexRequest,ListarPokesOpcionesRequest};
use App\Repositories\PokemonRepository;
class PokemonController extends Controller
{    
   protected PokemonRepository $pokemonRepository;

   public function __construct(PokemonRepository $pokemonRepository){
      $this->pokemonRepository = $pokemonRepository;
   }
   
   public function registrarPokemon(PokemonRequest $request){
      return $this->pokemonRepository->registrarPokemon($request);
   }

   public function actualizarPokemon(PokemonRequest $request){
      return $this->pokemonRepository->actualizarPokemon($request);
   }

   public function listarPokemones(ListarPokeRequest $request){
      return $this->pokemonRepository->listarPokemones($request);
   }

   public function EliminarPokemon(ListarPokeRequest $request){
      return $this->pokemonRepository->eliminarPokemon($request);
   }

   public function CargarPokemon(){
      return $this->pokemonRepository->cargarPokemones();
   }
   public function listarPokemonesPorRegion(ListarPokePorRegionRequest $request){
      return $this->pokemonRepository->listarPokemonesPorRegion($request);
   }
   public function listarPokemonesPorTipo(ListarPokePorTipoRequest $request){
      return $this->pokemonRepository->listarPokemonesPorTipo($request);
   }
   public function listarPokemonPorNombre(ListarPokePorNombreRequest $request){
      return $this->pokemonRepository->listarPokemonPorNombre($request);
   }
   public function listarPokemonPorPokedex(ListarPokePorPokedexRequest $request){
      return $this->pokemonRepository->listarPokemonPorPokedex($request);
   }
   public function listarPokemonesOpciones(ListarPokesOpcionesRequest $request){
      return $this->pokemonRepository->listarPokemonesOpciones($request);
   }
   
}

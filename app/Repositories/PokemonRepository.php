<?php

namespace App\Repositories;

use App\Jobs\CargaPokemonesJob;
use App\Models\Pokemon;
use App\Models\Region;
use App\Models\TipoPokemon;
use App\Services\PokemonService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PokemonRepository
{
    public function registrarPokemon($request)
    {
        try {
            $region = Region::where('reg_nombre', $request->region)->first();

            $pokemon = new Pokemon();
            $pokemon->nombre = $request->nombre;
            $pokemon->region_id = $region->id;
            $pokemon->save();
            return response()->json(["pokemon" => $pokemon], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                "error" => $e->getMessage(),
                "linea" => $e->getLine(),
                "file" => $e->getFile(),
                "metodo" => __METHOD__
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function actualizarPokemon($request)
    {
        try {
            $pokemon = Pokemon::find($request->id);
            $pokemon->nombre = $request->nombre;
            $pokemon->save();
            return response()->json(["pokemon" => $pokemon], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::info([
                "error" => $e->getMessage(),
                "linea" => $e->getLine(),
                "file" => $e->getFile(),
                "metodo" => __METHOD__
            ]);

            return response()->json([
                "error" => $e->getMessage(),
                "linea" => $e->getLine(),
                "file" => $e->getFile(),
                "metodo" => __METHOD__
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function listarPokemones($request)
    {
        try {
            $pokemon = Pokemon::whereIn('id', [3, 4, 5, 6, 7])->get();
            return response()->json(["pokemon" => $pokemon], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::info([
                "error" => $e->getMessage(),
                "linea" => $e->getLine(),
                "file" => $e->getFile(),
                "metodo" => __METHOD__
            ]);

            return response()->json([
                "error" => $e->getMessage(),
                "linea" => $e->getLine(),
                "file" => $e->getFile(),
                "metodo" => __METHOD__
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function eliminarPokemon($request)
    {
        try {
            $pokemon = Pokemon::find($request->id);
            $pokemon->delete();

            return response()->json(["pokemon" => $pokemon], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::info([
                "error" => $e->getMessage(),
                "linea" => $e->getLine(),
                "file" => $e->getFile(),
                "metodo" => __METHOD__
            ]);

            return response()->json([
                "error" => $e->getMessage(),
                "linea" => $e->getLine(),
                "file" => $e->getFile(),
                "metodo" => __METHOD__
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function cargarPokemones()
    {
        try {
            for ($i = 1; $i <= 9; $i++) {
              //$this->cargaPokemonPorRegion($i);
             CargaPokemonesJob::dispatch($i);
            }

            return response()->json(["ok"], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::info([
                "error" => $e->getMessage(),
                "linea" => $e->getLine(),
                "file" => $e->getFile(),
                "metodo" => __METHOD__
            ]);

            return response()->json([
                "error" => $e->getMessage(),
                "linea" => $e->getLine(),
                "file" => $e->getFile(),
                "metodo" => __METHOD__
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function cargaPokemonPorRegion($id)
    {
        $pokemonServiceRegion = new PokemonService;
        $pokemones = $pokemonServiceRegion->CargarRegiones($id);
        $region = new Region();
        $region->reg_nombre = $pokemones['body']['main_region']['name'];
        $region->save();
        foreach ($pokemones['body']['pokemon_species'] as $pokemon) {

            Log::info(["pokemon a revisar "=> $pokemon]);

            $idPokedex = str_replace('https://pokeapi.co/api/v2/pokemon-species/','', $pokemon['url']);
            
            $pokemonServiceTipo = new PokemonService;
            $pokemonTipo = $pokemonServiceTipo->CargarPokemonIndividual($idPokedex);

            Log::info([" poke x tipo"=> $pokemonTipo['body']['types'][0]['type']['name']]);
         
            $tipoUno = TipoPokemon::where('tip_nombre', $pokemonTipo['body']['types'][0]['type']['name'])->first();
            if(!$tipoUno){
                $tipoUno = new TipoPokemon();
                $tipoUno->tip_nombre = $pokemonTipo['body']['types'][0]['type']['name'];
                $tipoUno->save();
            }
            if(isset($pokemonTipo['body']['types'][1])){
                Log::info([" poke x tipo"=> $pokemonTipo['body']['types'][1]['type']['name']]);

                $tipoDos = TipoPokemon::where('tip_nombre', $pokemonTipo['body']['types'][1]['type']['name'])->first();
                if(!$tipoDos){
                    $tipoDos = new TipoPokemon();
                    $tipoDos->tip_nombre = $pokemonTipo['body']['types'][1]['type']['name'];
                    $tipoDos->save();
                }
            }

            $poke = new Pokemon();
            $poke->nombre = $pokemon['name'];
            $poke->region_id = $region->id;
            $poke->pokedex_number = intval($idPokedex);
            $poke->tipo_uno_id =$tipoUno->id;
            $poke->tipo_dos_id = isset($pokemonTipo['body']['types'][1]) ? $tipoDos->id : null;
            $poke->save();
        }
    }

    public function listarPokemonesPorRegion($request){
        try{
            // Obtén los pokemones utilizando el nombre proporcionado
            $pokemones = Pokemon::whereHas('region', function ($query) use ($request) {
                $query->where('reg_nombre', $request->region);
            })->get();
    
            if (!$pokemones) {
                return response()->json(['message' => 'Los pokemones no fueron encontrados'], 404);
            }
    
            return response()->json($pokemones, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al listar los Pokémon por región'], 500);
        }
    }

    public function listarPokemonesPorTipo($request){
        try{
            // Obtén los pokemones utilizando el nombre proporcionado
            $pokemones = Pokemon::whereHas('tipoPrimario', function ($query) use ($request) {
                $query->where('tip_nombre', $request->tipo);
            })->orWhereHas('tipoSecundario', function ($query) use ($request) {
                $query->where('tip_nombre', $request->tipo);
            })->get();

            if (!$pokemones) {
                return response()->json(['message' => 'Los pokemones no fueron encontrados'], 404);
            }
    
            return response()->json($pokemones, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al listar los Pokémon por tipo'], 500);
        }
    }

    public function listarPokemonPorNombre($request){
        try{
            // Obtén los pokemones utilizando el nombre proporcionado
            $pokemon = Pokemon::select('*')
                ->selectRaw("SOUNDEX(nombre) as name_soundex")
                ->whereRaw("SOUNDEX(nombre) = SOUNDEX(?)", [$request->nombre])
                ->orderByRaw("SOUNDEX(nombre) = SOUNDEX(?) DESC", [$request->nombre])
                ->with('region', 'tipoPrimario', 'tipoSecundario')
                ->first();
            if (!$pokemon) {
                return response()->json(['message' => 'El pokemon no fue encontrado'], 404);
            }
    
            return response()->json($pokemon, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al listar al pokemon'], 500);
        }
    }

    public function listarPokemonPorPokedex($request){
        try{
            // Obtén los pokemones utilizando el nombre proporcionado
            $pokemon = Pokemon::where('pokedex_number','=',$request->pokedex)->first();
    
            if (!$pokemon) {
                return response()->json(['message' => 'El pokemon no fue encontrado'], 404);
            }
    
            return response()->json($pokemon, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al listar el pokemon por número de pokedex'], 500);
        }
    }

    public function listarPokemonesOpciones($request){
        try {
            $query = Pokemon::query();
    
            if ($request->has('nombre')) {
                $query->where(function ($subquery) use ($request) {
                    $subquery->where('nombre', $request->nombre)
                            ->orWhereRaw("SOUNDEX(nombre) = SOUNDEX(?)", [$request->nombre]);
                });
            }
    
            if ($request->has('region')) {
                $query->whereHas('region', function ($subquery) use ($request) {
                    $subquery->where('reg_nombre', $request->region);
                });
            }
    
            if ($request->has('tipo')) {
                $query->where(function ($subquery) use ($request) {
                    $subquery->whereHas('tipoPrimario', function ($typeSubquery) use ($request) {
                        $typeSubquery->where('tip_nombre', $request->tipo);
                    })
                    ->orWhereHas('tipoSecundario', function ($typeSubquery) use ($request) {
                        $typeSubquery->where('tip_nombre', $request->tipo);
                    });
                });
            }
    
            if ($request->has('pokedex')) {
                $query->where('pokedex_number', $request->pokedex);
            }
    
            $pokemones = $query->orderBy('pokedex_number', 'asc')->get();
    
            if ($pokemones->isEmpty()) {
                return response()->json(['message' => 'Los pokemones no fueron encontrados'], 404);
            }
    
            return response()->json($pokemones, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al listar los Pokémon'], 500);
        }
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Comandos extends Controller
{
    public function index()
    {
        $titulo = 'Catálogo de Comandos';
        $datos = DB::table('comandos')->get();
        return view('modules.comandos.index', compact('titulo', 'datos'));
    }

    public function create()
    {
        $titulo = 'Nuevo Comando';
        return view('modules.comandos.create', compact('titulo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'comandos' => 'required|string|max:255', 
        ]);

        try {

            DB::table('comandos')->insert([
                'nombre'   => $request->nombre,   
                'comandos' => $request->comandos, 
            ]);
            return redirect()->route('comandos.index')->with('success', 'Comando guardado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $titulo = "Editar Comando";
        $item = DB::table('comandos')->where('id', $id)->first();
        return view('modules.comandos.edit', compact('titulo', 'item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'comandos' => 'required|string|max:255',
        ]);

        try {
            DB::table('comandos')->where('id', $id)->update([
                'nombre'   => $request->nombre,
                'comandos' => $request->comandos,
            ]);
            return redirect()->route('comandos.index')->with('success', 'Comando actualizado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar.');
        }
    }

    public function destroy($id)
    {
        try {
            // Verificamos si hay registros en la tabla pivote antes de borrar
            $enUso = DB::table('comando_componentes')->where('comando_id', $id)->exists();

            if ($enUso) {
                return back()->with('error', 'No se puede eliminar: el comando está vinculado a un componente.');
            }

            DB::table('comandos')->where('id', $id)->delete();
            return redirect()->route('comandos.index')->with('success', 'Eliminado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo eliminar.');
        }
    }
}
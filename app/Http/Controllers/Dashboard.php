<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;



class Dashboard extends Controller

{

    public function index()

    {

        return view('modules.dashboard.home');

    }

    public function pendiente(){

        $titulo = 'Vista Pendiente';

        return view('modules.pendiente.index', compact('titulo'));

    }

}
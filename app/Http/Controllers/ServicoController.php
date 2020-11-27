<?php

namespace App\Http\Controllers;

use App\Http\Requests\CriacaoServico;
use App\Models\Servico;
use Illuminate\Http\Request;

define('VIEW_LISTAR', 'servico.listar');
define('VIEW_CREATE', 'servico.create');

class ServicoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $servicosAtivos = Servico::all()->where('status', 'ATIVO');
            return view(VIEW_LISTAR, [
                'error' => false,
                'servicosAtivos' => $servicosAtivos
            ]);
        } catch (\Throwable $th) {
            return view(VIEW_LISTAR, [
                'error' => true,
                'message' => 'Ocorreu um erro ao listar os serviços disponíveis'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('servico.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CriacaoServico $request)
    {
        $request->validated();

        $servico = new Servico;
        $servico->descricao = $request->input('descricao');
        $servico->valor_unitario = $request->input('valorUnitario');
        $servico->status = $request->input('status');

        try {
            $servico->save();
            return view(VIEW_CREATE, [
                'servico' => $servico
            ]);
        } catch (\Throwable $th) {
            return view(VIEW_CREATE, [
                'failures' => ['Erro ao salvar o serviço']
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Servico  $servico
     * @return \Illuminate\Http\Response
     */
    public function show(Servico $servico)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Servico  $servico
     * @return \Illuminate\Http\Response
     */
    public function edit(Servico $servico)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Servico  $servico
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Servico $servico)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Servico  $servico
     * @return \Illuminate\Http\Response
     */
    public function destroy(Servico $servico)
    {
        //
    }
}

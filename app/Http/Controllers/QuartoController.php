<?php

namespace App\Http\Controllers;

use App\Models\Quarto;
use App\Models\Servico;
use Illuminate\Http\Request;

define('VIEW_CREATE', 'quarto.create');
define('VIEW_LISTAR', 'quarto.listar');

class QuartoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $quartosAtivos = Quarto::all()->where('status', 'ATIVO');
        return view(VIEW_LISTAR, [
            'quartosAtivos' => $quartosAtivos
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $servicos = Servico::where('status', 'ATIVO')->get();

        return view(VIEW_CREATE, [
            'servicos' => $servicos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $quarto = new Quarto;
        $quarto->andar = $request->andar;
        $quarto->status = strtoupper($request->status);
        $valorDiaria = $request->input('valorDiaria');
        $quarto->valor_diaria = $valorDiaria;

        $servicos = $request->input('servico');
        $servicosQuarto = array();

        if ($servicos != null && sizeof($servicos) > 0) {
            foreach ($servicos as $srv) {
                $s = Servico::find($srv);
                array_push($servicosQuarto, $s);
            }
        }

        try {
            $quarto->save();
            if (sizeof($servicosQuarto) > 0) {
                $quarto->servicos()->saveMany($servicosQuarto);
            }
            return view(VIEW_CREATE, [
                'message' => 'Quarto salvo com sucesso',
                'quarto' => $quarto,
                'error' => false
            ]);
        } catch (\Throwable $th) {
            echo $th->getMessage();
            return view(VIEW_CREATE, [
                'error' => true,
                'message' => 'Ocorreu um erro ao salvar o quarto'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quarto  $quarto
     * @return \Illuminate\Http\Response
     */
    public function show(Quarto $quarto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Quarto  $quarto
     * @return \Illuminate\Http\Response
     */
    public function edit(Quarto $quarto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quarto  $quarto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Quarto $quarto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Quarto  $quarto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quarto $quarto)
    {
        //
    }
}

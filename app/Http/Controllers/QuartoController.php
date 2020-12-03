<?php

namespace App\Http\Controllers;

use App\Http\Requests\CriacaoQuarto;
use App\Models\Quarto;
use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

define('VIEW_CREATE', 'quarto.create');
define('VIEW_LISTAR', 'quarto.listar');
define('MANUTENCAO_VIEW', 'quarto.manutencao');
class QuartoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        try {
            if ($status === null) {
                $quartosAtivos = Quarto::all()->where('status', 'ATIVO');
                return view(VIEW_LISTAR, [
                    'quartosAtivos' => $quartosAtivos
                ]);
            }

            if ($status === 'AGUARDANDO_LIMPEZA') {
                $quartosManutencao = Quarto::all()->where('status', $status);
                if (sizeof($quartosManutencao) > 0) {
                    return view(MANUTENCAO_VIEW, [
                        'quartos' => $quartosManutencao
                    ]);
                } else {
                    return view(MANUTENCAO_VIEW, [
                        'failures' => ['Não há quartos em manutenção']
                    ]);
                }
            }
        } catch (\Throwable $th) {
            $message = 'Não foi possível listar os quartos, tente novamente mais tarde';
            if ($status === null) {
                return view(VIEW_LISTAR, [
                    'failures' => [$message]
                ]);
            } else {
                return view(MANUTENCAO_VIEW, [
                    'failures' => [$message]
                ]);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $servicos = Servico::all()->where('status', 'ATIVO');
            return view(VIEW_CREATE, [
                'servicos' => $servicos
            ]);
        } catch (\Throwable $th) {
            return view(VIEW_CREATE, [
                'failures' => ['Não foi possível listar os quartos, tente novamente mais tarde']
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CriacaoQuarto $request)
    {
        $request->validated();

        $quarto = new Quarto;
        $quarto->andar = $request->input('andar');
        $quarto->status = strtoupper($request->input('status'));
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

    public function liberarQuarto($codigo)
    {
        $quarto = Quarto::find($codigo);
        $failures = [];

        if ($quarto == null) {
            array_push($failures, 'Quarto não encontrado');
        } else if ($quarto->status != 'AGUARDANDO_LIMPEZA') {
            array_push($failures, 'Quarto não está disponível para limpeza');
        }

        if (sizeof($failures) > 0) {
            return view(MANUTENCAO_VIEW, [
                'failures' => $failures
            ]);
        }

        try {
            $quarto->liberarQuarto();
            return view(MANUTENCAO_VIEW, [
                'message' => 'Quarto liberado com sucesso'
            ]);
        } catch (\Throwable $th) {
            Log::debug($th);
            return view(MANUTENCAO_VIEW, [
                'failures' => ['Erro liberar quarto']
            ]);
        }
    }
}

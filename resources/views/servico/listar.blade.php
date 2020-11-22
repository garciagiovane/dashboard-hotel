@extends('layout.layout')

@section('pageTitle', 'Consulta de serviços')

@section('show')
    <div class="alert alert-success">
        <h3>
            Serviços disponíveis
        </h3>
    </div>
    @if (!$error && sizeof($servicosAtivos))
        <div class="row">
            @foreach ($servicosAtivos as $servico)
                <div class="col-sm-6">
                    <div class="card border-success mb-3">

                        <div class="card-header text-white bg-success">
                            <h4>
                                {{ $servico->descricao }}
                            </h4>
                        </div>

                        <div class="card-body">
                            <h4>
                                <span class="badge badge-success">Código</span> 
                                <span style="font-size: 1.125rem">{{ $servico->id }}</span>
                            </h4>

                            <h4>
                                <span class="badge badge-success">Criado em</span>
                                <h5>{{ $servico->created_at }}</h5>
                            </h4>

                            <h4>
                                <span class="badge badge-success">Valor</span>
                                <span style="font-size: 1.125rem">{{ 'R$ ' . $servico->valor_unitario . '/dia' }}</span>
                            </h4>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>
            Não há serviços disponíveis
        </p>
    @endif

@endsection

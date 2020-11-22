@extends('layout.layout')

@section('pageTitle', 'Consulta de quartos')

@section('show')
    @isset($quartosAtivos)
        @if (sizeof($quartosAtivos))
            <div class="alert alert-success">
                <h3>
                    Quartos disponíveis
                </h3>
            </div>
            <div class="row">
                @foreach ($quartosAtivos as $quarto)

                    <div class="col-sm-6">
                        <div class="card border-success mb-3">
                            <div class="card-header text-white bg-success">
                                <h4>
                                    Quarto {{ $quarto->id }}
                                </h4>
                            </div>
                            <div class="card-body">
                                <h4>
                                    <span class="badge badge-success">Andar</span> <span style="font-size: 1.125rem">{{ $quarto->andar }}</span>
                                </h4>

                                <h4>
                                    <span class="badge badge-success">Criado em</span>
                                    <h5>{{ $quarto->created_at }}</h5>
                                </h4>

                                @if (sizeof($quarto->servicos) > 0)
                                    <h4>
                                        <span class="badge badge-success">Serviços disponíveis</span>
                                    </h4>
                                    <ul class="list-group list-group-horizontal-md">
                                        @foreach ($quarto->servicos as $srv)
                                            <li class="list-group-item"><b>{{ $srv->descricao }}</b></li>
                                        @endforeach
                                    </ul>

                                @endif
                            </div>

                        </div>
                    </div>

                @endforeach
            </div>
        @else
            <p>
                Não há quartos disponíveis
            </p>
        @endif
    @endisset
@endsection

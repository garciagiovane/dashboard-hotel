@extends('layout.layout')

@section('title', 'Cadastro de reservas')

@section('show')
    <div class="alert alert-success">
        <h3>
            Reservas
        </h3>
    </div>
    @isset($errors)

        @if (sizeof($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <hr>
                <p class="mb-0"><a class="alert-link" href="/reservations/create">Tente reservar novamente</a></p>
            </div>
        @endif

    @endisset

    @isset($reservation)
        <div class="alert alert-success">
            <p>
                Reserva efetuada com sucesso!
                <span>
                    Código da reserva: {{ $reservation->id }}
                </span>
            </p>
        </div>
    @endisset

    @if (isset($quartos))
        <div class="row">
            @foreach ($quartos as $quarto)
                <div class="col-sm-6">
                    <div class="card border-success mb-3">
                        <div class="card-header text-white bg-success">
                            <h4>
                                Quarto {{ $quarto->id }}
                            </h4>
                        </div>
                        <div class="card-body">
                            <h4>
                                <span class="badge badge-success">Criação: </span>
                                <h5>{{ $quarto->created_at }}</h5>
                            </h4>

                            <h4>
                                <span class="badge badge-success">Última atualização: </span>
                                <h5>{{ $quarto->updated_at }}</h5>
                            </h4>

                            <h4>
                                <span class="badge badge-success">Andar: </span>
                                <span style="font-size: 1.125rem">{{ $quarto->andar }}</span>
                            </h4>

                            <h4>
                                <span class="badge badge-success">Valor diária: </span>
                                <span style="font-size: 1.125rem">R$ {{ $quarto->valor_diaria }}</span>
                            </h4>

                            <div id={{ $quarto->id }} class="hide">
                                <h4>Reserva</h4>
                                <form action="/reservations" method="post">
                                    @csrf
                                    <input type="hidden" name="quartoId" value={{ $quarto->id }}>
                                    <div class="form-group">
                                        <label for="cpf">CPF cliente</label>
                                        <input type="number" class="form-control" name="cpf" id="cpf"
                                            placeholder="Ex: 12345678912" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="dias">Dias de reserva</label>
                                        <input type="number" class="form-control" name="dias" id="dias" placeholder="Ex: 1"
                                            required>
                                    </div>
                                    @if (sizeof($quarto->servicos) > 0)
                                        <div id="servicos">
                                            @foreach ($quarto->servicos as $servico)
                                                <input type="checkbox" value={{ $servico->id }}
                                                    name={{ 'servico[' . $servico->id . ']' }}>
                                                <label
                                                    for={{ 'servico[' . $servico->id . ']' }}>{{ $servico->descricao . ' ' . $servico->valor_unitario . '/dia' }}</label>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-primary">Reservar</button>
                                    </div>

                                    <div class="btn-group mr-2">
                                        <button type="button" onclick="esconderCadastro({{ $quarto->id }})"
                                            class="btn btn-danger">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="btn-toolbar">
                                <div class="btn-group mr-2">
                                    <button type="button" id={{ 'btn-reserva-' . $quarto->id }}
                                        onclick="mostrarCadastro(this, {{ $quarto->id }})" class="btn btn-primary">Cadastrar
                                        reserva</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-danger">
            <p>Não há quartos disponíveis</p>
        </div>
    @endif
    <style>
        .hide {
            display: none
        }

    </style>
    <script>
        function mostrarCadastro(button, idReserva) {
            document.getElementById(idReserva).className = 'show';
            button.classList.add('hide');
        }

        function esconderCadastro(idReserva) {
            document.getElementById(idReserva).className = 'hide';
            document.getElementById(`btn-reserva-${idReserva}`).classList.remove('hide');
        }

    </script>
@endsection

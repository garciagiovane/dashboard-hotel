@extends('layout.layout')

@section('pageTitle', 'Cadastro de quartos')

@section('show')
    <div class="alert alert-success">
        <h3>
            Cadastro de quartos
        </h3>
    </div>
    @isset($error)
        @if ($error)
            <div class="alert alert-danger">
                Ocorreu um erro ao salvar o quarto, {{ $message }}
            </div>
        @else
            <div class="alert alert-success">
                {{ $message }}
            </div>
        @endif
    @endisset

    <form action="/quartos" method="POST">
        @csrf
        <div class="form-group">
            <label for="andar">Andar</label>
            <input type="number" class="form-control" name="andar" id="andar" placeholder="Ex: 1" required>
        </div>

        <div class="form-group ">
            <label for="valorDiaria">Valor da diária</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">R$</span>
                </div>
                <input type="number" class="form-control" name="valorDiaria" id="valorDiaria" placeholder="Ex: 10" required>
                <div class="input-group-append">
                    <span class="input-group-text">,00</span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="custom-select custom-select-lg mb-3">
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
            </select>
        </div>

        @isset($servicos)
            @if (sizeof($servicos) > 0)
                <div id="servicos-disponiveis">
                    @foreach ($servicos as $srv)
                        <input type="checkbox" name={{ 'servico[' . $srv->id . ']' }} value={{ $srv->id }}>
                        <label for={{ 'servico[' . $srv->id . ']' }}> {{ $srv->descricao }}
                    @endforeach
                </div>
            @endif
        @endisset

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </div>

    </form>
@endsection

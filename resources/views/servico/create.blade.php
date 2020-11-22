@extends('layout.layout')

@section('pageTitle', 'Cadastro de serviços')

@section('show')
    <div class="alert alert-success">
        <h3>
            Cadastro de serviços
        </h3>
    </div>
    @isset($error)
        @if ($error)
            <div class="alert alert-danger">
                <p> {{ $message }} </p>
            </div>
        @else
            <div class="alert alert-success">
                <h4>Serviço cadastrado com sucesso</h4>
                <span class="badge badge-success">Código: </span> {{ $servico->id }}
                <span class="badge badge-success">Descrição: </span> {{ $servico->descricao }}
                <span class="badge badge-success">Valor unitário: </span> {{ $servico->valor_unitario }}
                <span class="badge badge-success">Status: </span> {{ $servico->status }}
            </div>
        @endif
    @endisset

    <form action="/servicos" method="post">
        @csrf
        <div class="form-group">
            <label for="descricao">Descrição</label>
            <input type="text" class="form-control" name="descricao" id="descricao" placeholder="Ex: Ar condicionado"
                required>
        </div>

        <div class="form-group ">
            <label for="valorUnitario">Valor unitário</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">R$</span>
                </div>
                <input type="text" class="form-control" name="valorUnitario" id="valorUnitario" placeholder="Valor unitário"
                    required>
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

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </div>
    </form>
@endSection

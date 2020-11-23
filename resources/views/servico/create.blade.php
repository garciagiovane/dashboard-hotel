@extends('layout.layout')

@section('pageTitle', 'Cadastro de serviços')

@section('show')
    <div class="alert alert-success">
        <h3>
            Cadastro de serviços
        </h3>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="list-group">
                @foreach ($errors->all() as $err)
                    <li class="list-group-item"> {{ $err }} </li>
                @endforeach
            </ul>
        </div>
    @endif

    @isset($failures)
        <div class="alert alert-danger">
            <ul class="list-group">
                @foreach ($failures as $failure)
                    <li class="list-group-item"> {{ $failure }} </li>
                @endforeach
            </ul>
        </div>
    @endisset

    @isset($servico)
        <div class="alert alert-success">
            <p>Serviço cadastrado com sucesso</p>
        </div>
    @endisset

    <form action="/servicos" method="post">
        @csrf
        <div class="form-group">
            <label for="descricao">Descrição</label>
            <input type="text" class="form-control" name="descricao" id="descricao" placeholder="Ex: Ar condicionado"
                required autocomplete="off">
        </div>

        <div class="form-group ">
            <label for="valorUnitario">Valor unitário</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">R$</span>
                </div>
                <input type="number" class="form-control" name="valorUnitario" id="valorUnitario"
                    placeholder="Valor unitário" required autocomplete="off">
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

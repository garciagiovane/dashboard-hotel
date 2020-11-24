@extends('layout.layout')

@section('title', 'Importação de clientes')

@section('show')

    <div class="alert alert-success">
        <h3>Importação de clientes</h3>
    </div>

    @isset($failures)
        <div class="alert alert-danger">
            <ul class="list-group">
                @foreach ($failures as $failure)
                    <li class="list-group-item">
                        Clinte {{ $failure->name }} não importado pelos seguintes motivos
                        <ul class="list-group">
                            @foreach ($failure->errors as $err)
                                <li class="list-group-item"> {{ $err }} </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
    @endisset

    @isset($message)
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endisset

    <form method="POST" action="/customers" enctype='multipart/form-data'>
        @csrf
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Arquivo CSV para importação</span>
            </div>
            <div class="custom-file">
                <input type="file" name="clientes-csv" class="form-control-file" id="inputGroupFile01">
            </div>
        </div>

        <input type="submit" class="btn btn-primary" value="Cadastrar">
    </form>
@endsection

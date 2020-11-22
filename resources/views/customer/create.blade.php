@extends('layout.layout')

@section('title', 'Importação de clientes')

@section('show')
    @isset($hasError)
        <div>{{ $errorMessage }}</div>
    @endisset
    <form method="POST" action="/customers" enctype='multipart/form-data'>
        @csrf
        <input type="text" name="name" id="name">
        <input type="file" name="clientes-csv">
        <input type="submit" value="Cadastrar">
    </form>
@endsection

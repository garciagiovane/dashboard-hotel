@extends('layout.layout')

@section('pageTitle', 'Quartos em manutenção')

@section('show')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="list-group">
                @foreach ($errors->all() as $err)
                    <li class="list-group-item">
                        {{ $err }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @isset($failures)
        <div class="alert alert-danger">
            <ul class="list-group">
                @foreach ($failures as $err)
                    <li class="list-group-item">
                        {{ $err }}
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

    @isset($quartos)
        <div class="alert alert-success">
            <h3>Quartos em manutenção</h3>
        </div>
        <table class="table">
            <thead>
                <th scope="col">Quarto</th>
                <th scope="col">Andar</th>
                <th scope="col">Status</th>
                <th scope="col">Ação</th>
            </thead>
            <tbody>
                @foreach ($quartos as $quarto)
                    <td>{{ $quarto->id }}</td>
                    <td>{{ $quarto->andar }}</td>
                    <td>{{ $quarto->status }}</td>
                    <td> <a href={{ '/quartos/' . $quarto->id . '/manutencao/encerrar' }}>Encerrar manutenção</a></td>
                @endforeach
            </tbody>
        </table>
    @endisset
@endsection

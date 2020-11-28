@extends('layout.layout')

@section('pageTitle', 'Check-in')

@section('show')
    <div class="alert alert-success">
        <h3>
            Check-in
        </h3>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Consulta de reservas</h3>
        </div>
        <div class="card-body">
            <form action="/reservations/checkin" method="post">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="reserva">CPF</label>
                    <input type="number" class="form-control" name="cpf" placeholder="Ex: 11111111111" required>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Fazer check-in</button>
                </div>
            </form>
        </div>
    </div>

    @if (isset($reservation))
    <div class="alert alert-success">
        Check-in efetuado!
    </div>
    @else
        @isset($reservations)
            <div class="card">
                <div class="card-header">
                    <h3>Reservas encontradas</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Reserva</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Status</th>
                                <th scope="col">Check-in</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reservations as $res)
                                <tr>
                                    <th scope="row"> {{ $res->id }} </th>
                                    <td>{{ $res->name }}</td>
                                    <td>{{ $res->status }}</td>
                                    <td><a href={{ '/reservations/checkin/' . $res->id }}>Fazer check-in</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endisset
    @endif

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
                    <li class="list-group-item">{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endisset
@endsection

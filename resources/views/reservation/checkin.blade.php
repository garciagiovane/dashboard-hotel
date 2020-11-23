@extends('layout.layout')

@section('pageTitle', 'Check-in')

@section('show')
    <div class="alert alert-success">
        <h3>
            Check-in
        </h3>
    </div>

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

    @isset($reservation)
        <div class="alert alert-success">
            <p>
                Checkin efetuado com sucesso!
            </p>
        </div>
    @endisset

    <div id="checkin">
        <form action="/reservations/checkin" method="post">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label for="reserva">Número da reserva</label>
                <input type="number" class="form-control" name="reserva" id="reserva" placeholder="Ex: 1" required>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Fazer check-in</button>
            </div>
        </form>
    </div>
@endsection

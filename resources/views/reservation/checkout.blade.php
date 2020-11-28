@extends('layout.layout')

@section('pageTitle', 'Check-out')

@section('show')
    <script
        src="https://www.paypal.com/sdk/js?client-id=Afs89w5mxJLFWj3PtbNjVL5-X7nukGhx-ynOSDDmkYQA1uGrKQE_jMq6eABP3JGRHwi-qZu-CVGat-0y&currency=BRL">

    </script>

    <div class="alert alert-success">
        <h3>
            Check-out
        </h3>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="list-group">
                @foreach ($errors->all() as $err)
                    <li class="list-group-item">{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3>Consulta de reservas</h3>
        </div>
        <div class="card-body">
            <form action="/reservations/checkout" method="post">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="reserva">CPF</label>
                    <input type="number" class="form-control" name="cpf" placeholder="Ex: 11111111111" required autocomplete="off">
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Fazer check-out</button>
                </div>
            </form>
        </div>
    </div>

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
                            <th scope="col">Check-in</th>
                            <th scope="col">Check-out</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Status</th>
                            <th scope="col">Pagamento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservations as $res)
                            <tr>
                                <th scope="row"> {{ $res->id }} </th>
                                <td>{{ $res->data_checkin }}</td>
                                <td>{{ $res->data_checkout }}</td>
                                <td>{{ $res->name }}</td>
                                <td>{{ $res->status }}</td>
                                <td><a href={{ '/reservations/payment/' . $res->id }}>Pagar</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endisset

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

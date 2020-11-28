@extends('layout.layout')

@section('pageTitle', 'Pagamento')

@section('show')
    <script
        src="https://www.paypal.com/sdk/js?client-id=Afs89w5mxJLFWj3PtbNjVL5-X7nukGhx-ynOSDDmkYQA1uGrKQE_jMq6eABP3JGRHwi-qZu-CVGat-0y&currency=BRL">

    </script>

    <div class="alert alert-success">
        <h3>
            Pagamento
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

    @isset($failures)
        <div class="alert alert-danger">
            <ul class="list-group">
                @foreach ($failures as $err)
                    <li class="list-group-item">{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endisset

    <div id="mensagem-reserva" class="alert alert-info hide"></div>

    <style>
        td {
            width: 200px
        }

        .hide {
            display: none;
        }

    </style>

    @isset($reservation)
        <div id="detalhes-reserva" class="card">
            <div class="card-header">
                <h3>Totais</h3>
            </div>
            <div class="card-body">
                <table>
                    <tbody>
                        <tr>
                            <td><strong>Reserva</strong></td>
                            <td>{{ $reservation->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Check-in</strong></td>
                            <td>{{ date_format(date_create($reservation->data_checkin), 'd-m-Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Check-out</strong></td>
                            <td>{{ $reservation->data_checkout->format('d-m-Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dias da reserva</strong></td>
                            <td>{{ $reservation->dias }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td>R$ {{ $reservation->total_reserva }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>{{ $reservation->status }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @php
        echo '
        <script>
            var valorTotal = ' . $reservation->total_reserva . ';
            var codigoReserva = ' . $reservation->id . ';

        </script>
        ';
        @endphp
        @include('reservation.paypal')
    @endisset
@endsection

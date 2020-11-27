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

    @isset($failures)
        <div class="alert alert-danger">
            <ul class="list-group">
                @foreach ($failures as $err)
                    <li class="list-group-item">{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endisset

    @isset($reservation)
        <div id="mensagem-reserva" class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
        <div class="card">
            <div class="card-header">
                <h3>Resumo da reserva {{ $reservation->id }}</h3>
            </div>
            <div class="card-body">
                <h4><span class="badge badge-info">Valor total: </span> R$ {{ $reservation->total_reserva }}</h4>
                <h4><span class="badge badge-info">Quarto: </span> {{ $reservation->quarto_id }}</h4>
            </div>
        </div>
    @endisset

    @if (!isset($reservation))
        <form action="/reservations/checkout" method="post">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label for="reserva">Número da reserva</label>
                <input type="number" class="form-control" name="reserva" placeholder="Ex: 1" required>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Fazer check-out</button>
            </div>
        </form>
    @endif

    @isset($reservation)
        <div class="card">
            <div class="card-header">
                <h3>Pagamento</h3>
            </div>
            <div class="card-body">
                <div id="paypal-button-container"></div>
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

        <script>
            var element = document.getElementById('mensagem-reserva');

            paypal.Buttons({
                createOrder: function(data, actions) {
                    // This function sets up the details of the transaction, including the amount and line item details.
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: valorTotal
                            }
                        }]
                    });
                },
                onError: function(err) {
                    // Show an error page here, when an error occurs
                    element.className = 'alert alert-danger';
                    element.innerHTML =
                        `<p>Ocorreu um erro no pagamento, por favor, tente novamente</p>`;
                },
                onApprove: function(data, actions) {
                    // This function captures the funds from the transaction.
                    return actions.order.capture().then(function(details) {
                        // This function shows a transaction success message to your buyer.
                        element.className = 'alert alert-success';
                        element.innerHTML = `<p>Reserva ${codigoReserva} paga com sucesso</p>`;
                        document.getElementById('paypal-button-container').className = 'hide';

                        fetch(`/api/reservations/checkout/${codigoReserva}`, {
                                method: 'PATCH'
                            })
                            .then(response => {
                                if (response.status) {
                                    console.log('Sucesso na atualização da reserva');
                                } else {
                                    console.log('Erro ao atualizar reserva');
                                }
                            })
                            .catch(response => console.log('Ocorreu um erro na operação'));
                    });
                }
            }).render("#paypal-button-container");

        </script>
    @endisset
@endsection

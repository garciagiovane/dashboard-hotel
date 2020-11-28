@section('payment')
    <div id="payment" class="card">
        <div class="card-header">
            <h3>Pagamento</h3>
        </div>
        <div class="card-body">
            <div id="paypal-button-container"></div>
        </div>
    </div>
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
                    `<p>Ocorreu um erro no pagamento, por favor, <a href="/reservations/payment/${codigoReserva}">tente novamente</a></p>`;
            },
            onApprove: function(data, actions) {
                // This function captures the funds from the transaction.
                return actions.order.capture().then(function(details) {
                    // This function shows a transaction success message to your buyer.
                    element.className = 'alert alert-success';
                    element.innerHTML = `<p>Pagamento efetuado com sucesso</p>`;
                    document.getElementById('payment').className = 'hide';
                    document.getElementById('detalhes-reserva').className = 'hide';

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
            },
            onCancel: function(data) {
                element.className = 'alert alert-danger';
                element.innerHTML = `<p>O pagamento não foi concluído, por favor, tente novamente</p>`;
            }
        }).render("#paypal-button-container");

    </script>
@show

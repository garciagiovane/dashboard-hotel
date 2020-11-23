<?php

namespace App\Http\Controllers;

use App\Http\Requests\CriacaoReserva;
use App\Models\Customer;
use App\Models\Quarto;
use App\Models\Reservation;
use App\Models\Servico;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

define('CREATE_RESERVATION_VIEW', 'reservation.create');
define('CREATE_CHECKIN_VIEW', 'reservation.checkin');
define('CREATE_CHECKOUT_VIEW', 'reservation.checkout');
define('AGUARDANDO', 'AGUARDA_CONFIRMACAO');
define('PAYMENT_VIEW', 'reservation.payment');
define('ATIVO', 'ATIVO');

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $quartosAtivos = Quarto::all();
        $quartosAtivosTrue = array();

        foreach ($quartosAtivos as $quarto) {
            if ($quarto->status == 'RESERVADO' && isset($quarto->data_prevista_checkin)) {
                $dataAtual = new DateTime();
                $response = $dataAtual->diff(new DateTime($quarto->data_prevista_checkin));
                $diasDiferenca = $response->d;

                if ($diasDiferenca > 1) {
                    Reservation::cancelarReserva($quarto->reservation_id);
                    Quarto::liberarQuarto($quarto);
                }
            }

            if ($quarto->status == 'ATIVO') {
                array_push($quartosAtivosTrue, $quarto);
            }
        }

        if (sizeof($quartosAtivosTrue) > 0) {
            return view(CREATE_RESERVATION_VIEW, [
                'quartos' => $quartosAtivosTrue
            ]);
        } else {
            return view(CREATE_RESERVATION_VIEW, [
                'failures' => ['Não há quartos disponíveis']
            ]);
        }
    }

    private function getClienteBy(int $cpf)
    {
        return Customer::where('cpf', $cpf)->where('status', ATIVO)->first();
    }

    private function getQuartoBy(int $id)
    {
        return Quarto::where('id', $id)->where('status', ATIVO)->first();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CriacaoReserva $request)
    {
        $request->validated();

        $inputServicos = $request->input('servico');
        $diasReserva = $request->input('dias');

        $servicos = array();
        $errors = array();

        if ($inputServicos != null) {
            foreach ($inputServicos as $serviceCode) {
                $servico = Servico::find($serviceCode);
                if ($servico != null) {
                    array_push($servicos, $servico);
                } else {
                    array_push($errors, 'O serviço ' . $serviceCode . ' não foi encontrado');
                }
            }
        }

        $quarto = $this->getQuartoBy($request->input('quartoId'));
        if ($quarto === null) {
            array_push($errors, 'Quarto indisponível');
        }

        $cliente = $this->getClienteBy($request->input('cpf'));
        if ($cliente == null) {
            array_push($errors, 'Cliente indisponível');
        }

        if (sizeof($errors) > 0) {
            return view(CREATE_RESERVATION_VIEW, [
                'message' => 'Não foi possível realizar a reserva',
                'failures' => $errors
            ]);
        }

        $reservation = new Reservation;
        $reservation->customer_id = $cliente->id;
        $reservation->quarto_id = $quarto->id;
        $reservation->dias = $diasReserva;
        $reservation->total_reserva = $quarto->valor_diaria * $diasReserva;
        $reservation->status = AGUARDANDO;

        $quarto->status = 'RESERVADO';
        $dataEsperadaCheckin = new DateTime();
        $quarto->data_prevista_checkin = $dataEsperadaCheckin->add(new DateInterval('P1D'));
        $quantiadeServicos = sizeof($servicos);

        try {
            $reservation->save();
            $quarto->reservation_id = $reservation->id;
            $quarto->save();

            if ($quantiadeServicos > 0) {
                foreach ($servicos as $srv) {
                    DB::table('reservas_quartos')->insert([
                        'reservation_id' => $reservation->id,
                        'servico_id' => $srv->id,
                        'valor_servico' => $srv->valor_unitario * $diasReserva
                    ]);
                }
            }

            return view(CREATE_RESERVATION_VIEW, [
                'reservation' => $reservation
            ]);
        } catch (\Throwable $th) {
            return view(CREATE_RESERVATION_VIEW, [
                'message' => 'Ocorreu um erro ao criar a reserva ' . $th->getMessage(),
                'failures' => ['Ocorreu um erro ao criar a reserva ', $th->getMessage()]
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function show(Reservation $reservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reservation $reservation)
    {
    }

    public function atualizarReserva($codigoReserva)
    {

        $reservation = Reservation::find($codigoReserva);
        $reservation->status = 'ENCERRADA';
        $reservation->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reservation $reservation)
    {
        //
    }

    public function showcheckin()
    {
        return view('reservation.checkin');
    }

    private function getDoCheckinValidator(Request $request)
    {
        return $this->getValidator($request, [
            'reserva' => 'required|numeric|min:1'
        ], [
            'required' => ':attribute é obrigatório',
            'numeric' => ':attribute deve ser numérico',
            'min' => 'Número da reserva deve ser maior que zero'
        ]);
    }

    private function getValidator(Request $request, $rules, $messages)
    {
        return Validator::make(
            $request->all(),
            $rules,
            $messages
        );
    }

    public function docheckin(Request $request)
    {
        $this->getDoCheckinValidator($request)->validate();

        $idReserva = $request->input('reserva');
        $errors = array();

        $reservation = Reservation::find($idReserva);

        if ($reservation != null) {
            if ($reservation->status == 'CANCELADA') {
                array_push($errors, 'Reserva cancelada');
            }

            if ($reservation->status == 'CONFIRMADA') {
                array_push($errors, 'Reserva já foi confirmada');
            }
        } else {
            array_push($errors, 'Reserva não encontrada');
        }

        if (sizeof($errors) > 0) {
            return view(CREATE_CHECKIN_VIEW, [
                'failures' => $errors
            ]);
        }

        $quarto = Quarto::find($reservation->quarto_id);
        $quarto->status = 'OCUPADO';
        $quarto->save();

        $reservation->data_checkin = new DateTime();
        $reservation->status = 'CONFIRMADA';
        $reservation->save();

        return view(CREATE_CHECKIN_VIEW, [
            'reservation' => $reservation
        ]);
    }

    public function showcheckout()
    {
        return view(CREATE_CHECKOUT_VIEW);
    }

    public function docheckout(Request $request)
    {
        $this->getDoCheckinValidator($request)->validate();
        $reserva = $request->input('reserva');

        $errors = array();

        $reservation = Reservation::find($reserva);

        if ($reservation != null) {
            if ($reservation->status == 'ENCERRADA') {
                array_push($errors, 'Reserva já encerrada');
            }

            if ($reservation->status == 'AGUARDANDO_PAGAMENTO') {
                array_push($errors, 'Reserva está aguardando pagamento');
            }
        } else {
            array_push($errors, 'Reserva não encontrada');
        }

        if (sizeof($errors) > 0) {
            return view(CREATE_CHECKOUT_VIEW, [
                'failures' => $errors
            ]);
        }

        $quarto = Quarto::find($reservation->quarto_id);

        $reservation->status = 'AGUARDANDO_PAGAMENTO';
        Quarto::liberarQuarto($quarto);
        $servicos = $reservation->servicos;
        $total = $reservation->total_reserva;

        if ($servicos != null) {
            $totalServicos = 0;

            foreach ($servicos as $srv) {
                $totalServicos += $srv->valor_unitario * $reservation->dias;
            }
            $total += $totalServicos;
        }

        $reservation->total_reserva = $total;
        $reservation->data_checkout = new DateTime();
        $reservation->save();

        return view(CREATE_CHECKOUT_VIEW, [
            'reservation' => $reservation,
            'message' => 'Reserva aguardando pagamento'
        ]);
    }

    public function showPayment()
    {
        return view(PAYMENT_VIEW);
    }

    public function doPayment(Request $request)
    {
        $reserva = $request->input('reserva');

        if ($reserva == null) {
            return view(PAYMENT_VIEW, [
                'errors' => array('Número da reserva é obrigatório!')
            ]);
        }

        $reservation = Reservation::find($reserva);
        $errors = array();

        if ($reservation != null) {
            if ($reservation->status != 'AGUARDANDO_PAGAMENTO') {
                array_push($errors, 'Reserva não está disponível para pagamento!');
            }
        } else {
            array_push($errors, 'Reserva não encontrada!');
        }

        if (sizeof($errors) > 0) {
            return view(PAYMENT_VIEW, [
                'errors' => $errors
            ]);
        }

        return view(PAYMENT_VIEW, [
            'reservation' => $reservation
        ]);
    }
}

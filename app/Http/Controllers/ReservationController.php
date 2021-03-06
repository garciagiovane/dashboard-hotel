<?php

namespace App\Http\Controllers;

use App\Http\Requests\CriacaoReserva;
use App\Models\Customer;
use App\Models\Quarto;
use App\Models\Reservation;
use App\Models\Servico;
use DateInterval;
use DateTime;
use DateTimeZone;
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
define('REGIAO_BRASIL', 'America/Sao_Paulo');
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
                    $quarto->liberarQuarto();
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
        $dataAtual = new DateTime('now', new DateTimeZone(REGIAO_BRASIL));

        $inputServicos = $request->input('servico');

        $servicos = array();
        $errors = array();

        $dataCheckin = $request->input('data-checkin');

        $dateTimeCheckin = DateTime::createFromFormat('Y-m-d\TH:i', $dataCheckin, new DateTimeZone(REGIAO_BRASIL));
        $dateTimeCheckin->add(new DateInterval('PT' . 5 . 'M'));
        if ($dataCheckin == null || $dateTimeCheckin < $dataAtual) {
            array_push($errors, 'Data check-in inválida');
        }

        $dataCheckout = $request->input('data-checkout');
        $dateTimeCheckout = DateTime::createFromFormat('Y-m-d\TH:i', $dataCheckout, new DateTimeZone(REGIAO_BRASIL));
        $dateTimeCheckout->add(new DateInterval('PT' . 5 . 'M'));
        if ($dataCheckin == null || $dateTimeCheckout < $dataAtual) {
            array_push($errors, 'Data check-out inválida');
        }
        $dateDiff = $dateTimeCheckout->diff($dateTimeCheckin)->days;
        $diasReserva = $dateDiff < 1 ? 1 : $dateDiff;
        if ($dateTimeCheckout < $dateTimeCheckin) {
            array_push($errors, 'Checkout não pode ser antes do checkin');
        }

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

        $dateDiff = $dateTimeCheckout->diff($dateTimeCheckin)->days;
        $diasReserva = $dateDiff < 1 ? 1 : $dateDiff;

        $reservation = new Reservation;
        $reservation->customer_id = $cliente->id;
        $reservation->quarto_id = $quarto->id;
        $reservation->dias = $diasReserva;
        $reservation->total_reserva = $quarto->valor_diaria * $diasReserva;
        $reservation->status = AGUARDANDO;

        $quarto->status = 'RESERVADO';
        $dataEsperadaCheckin = $dataAtual;
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
            'cpf' => 'required|string|size:11'
        ], [
            'required' => ':attribute é obrigatório',
            'size' => 'CPF inválido'
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

    public function efetuarCheckin($reserva)
    {
        $errors = array();
        if ($reserva == null) {
            array_push($errors, 'Código da reserva inválido');
        } else {
            $reservation = Reservation::find($reserva);

            if ($reservation == null) {
                array_push($errors, 'Reserva não encontrada');
            } else if ($reservation->status != 'AGUARDA_CONFIRMACAO') {
                array_push($errors, 'Reserva não está disponível para checkin');
            }

            if (sizeof($errors) > 0) {
                return view(CREATE_CHECKIN_VIEW, [
                    'failures' => $errors
                ]);
            } else {
                $quarto = Quarto::find($reservation->quarto_id);
                $quarto->status = 'OCUPADO';
                $quarto->save();

                $reservation->data_checkin = new DateTime('now', new DateTimeZone(REGIAO_BRASIL));
                $reservation->status = 'CONFIRMADA';
                $reservation->save();

                return view(CREATE_CHECKIN_VIEW, [
                    'reservation' => $reservation
                ]);
            }
        }
    }

    public function showReservationsCheckin(Request $request)
    {
        $this->getDoCheckinValidator($request)->validated();
        $cpf = $request->input('cpf');

        $reservasCliente = DB::table('reservations')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->select('reservations.*', 'customers.name')
            ->where('customers.cpf',  '=', $cpf)
            ->where('reservations.status', '=', 'AGUARDA_CONFIRMACAO')
            ->get();

        if (sizeof($reservasCliente) > 0) {
            return view(CREATE_CHECKIN_VIEW, [
                'reservations' => $reservasCliente
            ]);
        }

        return view(CREATE_CHECKIN_VIEW, [
            'failures' => ['Nenhuma reserva disponível para checkin']
        ]);
    }

    public function showcheckout()
    {
        return view(CREATE_CHECKOUT_VIEW);
    }

    function docheckout(Request $request)
    {
        $this->getDoCheckinValidator($request)->validate();
        $cpf = $request->input('cpf');

        $reservasCliente = DB::table('reservations')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->select('reservations.*', 'customers.name')
            ->where('customers.cpf',  '=', $cpf)
            ->where('reservations.status', '=', 'CONFIRMADA')
            ->orWhere('reservations.status', '=', 'AGUARDANDO_PAGAMENTO')
            ->get();

        if (sizeof($reservasCliente) > 0) {
            return view(CREATE_CHECKOUT_VIEW, [
                'reservations' => $reservasCliente,
                'message' => 'Reserva aguardando pagamento'
            ]);
        }

        return view(CREATE_CHECKOUT_VIEW, [
            'failures' => ['Nenhuma reserva encontrada']
        ]);
    }

    public function showPayment()
    {
        return view(PAYMENT_VIEW);
    }

    public function payment($reserva)
    {
        $reservation = Reservation::find($reserva);
        if ($reservation == null) {
            return view(CREATE_CHECKOUT_VIEW, [
                'failures' => ['Reserva não encontrada']
            ]);
        }

        if ($reservation->status == 'ENCERRADA') {
            return view(CREATE_CHECKOUT_VIEW, ['failures' => ['Reserva já encerrada']]);
        }

        $dataCheckout = new DateTime('now', new DateTimeZone(REGIAO_BRASIL));
        $response = $dataCheckout->diff(new DateTime($reservation->data_checkin));
        $diasReserva = $response->d < 1 ? 1 : $response->d;
        $reservation->dias = $diasReserva;
        $quarto = Quarto::find($reservation->quarto_id);

        $reservation->total_reserva = $reservation->dias * $quarto->valor_diaria;

        $reservation->status = 'AGUARDANDO_PAGAMENTO';

        $quarto->limparQuarto();
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
        $reservation->data_checkout = $dataCheckout;
        $reservation->save();

        return view(PAYMENT_VIEW, [
            'reservation' => $reservation
        ]);
    }

    public function doPayment(Request $request)
    {
        $reserva = $request->input('reserva');
        Validator::validate($request->all(), [
            'reserva' => 'required'
        ], [
            'required' => ':attribute é obrigatório'
        ]);

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
                'failures' => $errors
            ]);
        }

        return view(PAYMENT_VIEW, [
            'reservation' => $reservation
        ]);
    }
}

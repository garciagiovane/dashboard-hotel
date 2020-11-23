<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

define('VIEW_CREATE', 'customer.create');

class CustomerController extends Controller
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
        return view(VIEW_CREATE);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = $request->file('clientes-csv');

        if ($file !== null) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $valid_extension = array("csv");

            if (!in_array($extension, $valid_extension)) {
                return view(VIEW_CREATE, [
                    'failures' => ['Formato do arquivo não suportado']
                ]);
            }

            $location = 'uploads';
            $file->move($location, $filename);

            $filePath = public_path($location . '/' . $filename);

            $file = fopen($filePath, 'r');
            $i = 0;
            $importData_arr = array();

            while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                $num = count($filedata);

                // Skip first row (Remove below comment if you want to skip the first row)
                //if($i == 0){
                // $i++;
                //continue; 
                //}

                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }

            fclose($file);
            $failures = [];

            foreach ($importData_arr as $importData) {

                $cpf = $importData[1];
                $name = $importData[0];
                $phone = $importData[2];
                $zipcode = $importData[3];
                $status = strtoupper($importData[4]);

                if (strlen($cpf) == 11) {
                    $customer = new Customer;

                    $customer->name = $name;
                    $customer->cpf = $cpf;
                    $customer->phone = $phone;
                    $customer->zipCode = $zipcode;
                    $customer->status = strtoupper($status);

                    try {
                        $customer->save();
                    } catch (\Throwable $th) {
                        array_push($failures, 'Cliente ' . $name . ' não cadastrado, motivo: falha na persistência');
                    }
                } else {
                    array_push($failures, 'Cliente ' . $name . ' não cadastrado, motivo: CPF inválido');
                }
            }

            if (sizeof($failures) > 0) {
                return view(VIEW_CREATE, [
                    'failures' => $failures
                ]);
            }

            return view(VIEW_CREATE, [
                'message' => 'Clientes cadastrados com sucesso'
            ]);
        }

        return view(VIEW_CREATE, [
            'failures' => ['Não foi possível carregar o arquivo de clientes']
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerErrors;
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
        $lines = file($file->getRealPath());
        $errors = [];

        foreach ($lines as $line) {
            $columns = str_getcsv($line, getenv('DELIMITER'));

            $messageErrors = [];
            $customerError = null;

            $name = $columns[0];
            $cpf = $columns[1];
            $phone = $columns[2];
            $cep = $columns[3];
            $status = $columns[4];

            $this->validateCpf($cpf, $messageErrors);
            $this->validatePhone($phone, $messageErrors);
            $this->validateZipCode($cep, $messageErrors);
            $this->validateStatus($status, $messageErrors);

            if (sizeof($messageErrors) > 0) {
                $customerError = new CustomerErrors;
                $customerError->name = $name;
                $customerError->errors = $messageErrors;

                array_push($errors, $customerError);
            } else {
                $customer = new Customer;
                $customer->name = $name;
                $customer->cpf = $cpf;
                $customer->phone = $phone;
                $customer->zipCode = $cep;
                $customer->status = $status;

                try {
                    $customer->save();
                } catch (\Throwable $th) {
                    $customerError = new CustomerErrors;
                    $customerError->name = $name;
                    $customerError->errors = ['Falha na persistência dos dados'];

                    array_push($errors, $customerError);
                }
            }
        }

        if (sizeof($errors) > 0) {
            return view(VIEW_CREATE, [
                'failures' => $errors
            ]);
        } else {

            return view(VIEW_CREATE, [
                'message' => 'Importação realizada com sucesso!'
            ]);
        }
    }

    private function validateStatus($status, $errors)
    {
        if (strlen($status) > 10) {
            array_push($errors, 'Status inválido');
        }
    }

    private function validateZipCode($zipCode, $errors)
    {
        $hasError = false;
        if (!is_numeric($zipCode)) {
            $hasError = true;
        }

        if (strlen($zipCode) != 8) {
            $hasError = true;
        }

        if ($hasError) {
            array_push($errors, 'CEP inválido');
        }
    }

    private function validatePhone($phone, $errors)
    {
        if (!is_numeric($phone)) {
            array_push($errors, 'Telefone inválido');
        }
    }

    private function validateCpf($cpf, $errors)
    {
        $hasError = false;
        if (!is_numeric($cpf)) {
            $hasError = true;
        }

        if (strlen($cpf) != 11) {
            $hasError = true;
        }

        if ($hasError) {
            array_push($errors, 'CPF inválido');
        }
    }
    // public function store(Request $request)
    // {
    //     $file = $request->file('clientes-csv');

    //     if ($file !== null) {
    //         $filename = $file->getClientOriginalName();
    //         $extension = $file->getClientOriginalExtension();

    //         $valid_extension = array("csv");

    //         if (!in_array($extension, $valid_extension)) {
    //             return view(VIEW_CREATE, [
    //                 'failures' => ['Formato do arquivo não suportado']
    //             ]);
    //         }

    //         $location = 'uploads';
    //         $file->move($location, $filename);

    //         $filePath = public_path($location . '/' . $filename);

    //         $file = fopen($filePath, 'r');
    //         $i = 0;
    //         $importData_arr = array();

    //         while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
    //             $num = count($filedata);

    //             // Skip first row (Remove below comment if you want to skip the first row)
    //             //if($i == 0){
    //             // $i++;
    //             //continue; 
    //             //}

    //             for ($c = 0; $c < $num; $c++) {
    //                 $importData_arr[$i][] = $filedata[$c];
    //             }
    //             $i++;
    //         }

    //         fclose($file);
    //         $failures = [];

    //         foreach ($importData_arr as $importData) {

    //             $cpf = $importData[1];
    //             $name = $importData[0];
    //             $phone = $importData[2];
    //             $zipcode = $importData[3];
    //             $status = strtoupper($importData[4]);

    //             if (strlen($cpf) == 11) {
    //                 $customer = new Customer;

    //                 $customer->name = $name;
    //                 $customer->cpf = $cpf;
    //                 $customer->phone = $phone;
    //                 $customer->zipCode = $zipcode;
    //                 $customer->status = strtoupper($status);

    //                 try {
    //                     $customer->save();
    //                 } catch (\Throwable $th) {
    //                     array_push($failures, 'Cliente ' . $name . ' não cadastrado, motivo: falha na persistência');
    //                 }
    //             } else {
    //                 array_push($failures, 'Cliente ' . $name . ' não cadastrado, motivo: CPF inválido');
    //             }
    //         }

    //         if (sizeof($failures) > 0) {
    //             return view(VIEW_CREATE, [
    //                 'failures' => $failures
    //             ]);
    //         }

    //         return view(VIEW_CREATE, [
    //             'message' => 'Clientes cadastrados com sucesso'
    //         ]);
    //     }

    //     return view(VIEW_CREATE, [
    //         'failures' => ['Não foi possível carregar o arquivo de clientes']
    //     ]);
    // }

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

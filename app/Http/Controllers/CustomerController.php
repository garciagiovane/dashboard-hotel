<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
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
        return Customer::all();
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
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();


            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;

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

            foreach ($importData_arr as $importData) {
                $customer = new Customer;

                $customer->name = $importData[0];
                $customer->cpf = $importData[1];
                $customer->phone = $importData[2];
                $customer->zipCode = $importData[3];
                $customer->status = strtoupper($importData[4]);

                $customer->save();
            }

            return view(VIEW_CREATE, [
                'message' => 'Cliente cadastrado com suesso'
            ]);
        }

        return view(VIEW_CREATE, [
            'hasError' => true,
            'errorMessage' => 'Não foi possível carregar a base de clientes'
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
        $error = null;
        $customer = null;
        try {
            $customer = Customer::find($id);
        } catch (\Throwable $th) {
            $error = [
                'message' => $th->getMessage(),
                'errorCode' => $th->getCode()
            ];
        }

        return view('customer.show-by-id', [
            'customer' => $customer,
            'error' => $error
        ]);
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

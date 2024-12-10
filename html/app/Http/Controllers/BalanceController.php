<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\models\Balance;

class BalanceController extends Controller
{
    //зачисление на баланс
    public function cashIn(Request $request) {

        $request->validate([
            'user_id' => 'required',
            'sum' => 'required|numeric',
        ]);

        $data = new Balance();
        $data->user_id = $request->user_id;
        $data->sum = $request->sum;
        $data->status = 'cashin';
        $data->save();

        return response()->json([
            'status' => true,
            'data' => $data 
        ], 201);
    }

    //списание с баланса
    public function cashOut(Request $request) {

        $request->validate([
            'user_id' => 'required',
            'sum' => 'required|numeric',
        ]);

        $sum =  DB::table('balances')->select('sum')->where('user_id', '=', $request->user_id)->sum('sum');
        if ($sum >= $request->sum) {

            $data = new Balance();
            $data->user_id = $request->user_id;
            $data->sum = $request->sum * -1;
            $data->status = 'cashout';
            $data->save();

            return response()->json([
                'status' => true,
                'data' => $data
            ], 201);
        }
    }

    //перевод
    public function transfer(Request $request) {

        $request->validate([
            'user_id' => 'required',
            'sum' => 'required|numeric',
            'user_id_transfer' => 'required'
        ]);

        $sum =  DB::table('balances')->select('sum')->where('user_id', '=', $request->user_id)->sum('sum');
        if ($sum >= $request->sum) {

            DB::table('balances')->insert([
                //списание
                ['user_id' => $request->user_id, 'sum' => $request->sum * -1, "status" => 'transfer_cashout', "user_id_transfer" => $request->user_id_transfer],
                //зачисление
                ['user_id' => $request->user_id_transfer, 'sum' => $request->sum, "status" => 'transfer_cashin', "user_id_transfer" => $request->user_id]
            ]);

            return response()->json([
                'status' => true,
                'data1' => $data1,
                'data2' => $data2
            ], 201);
        }
    }

    //получение баланса
    public function getBalance(Request $request) {

        $data =  DB::table('balances')->select('sum')->where('user_id', '=', $request->user_id)->sum('sum');

        return response()->json([
            'status' => true,
            'balance' => $data
        ], 200);
    }
}

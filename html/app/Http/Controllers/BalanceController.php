<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

use App\models\Balance;

class BalanceController extends Controller
{
    //зачисление на баланс
    public function cashIn(Request $request) {

        $request->validate([
            'user_id' => 'required',
            'sum' => 'required|numeric',
        ], [
            'user_id.required' => 'Need user_id'
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

        $sum = DB::table('balances')->select('sum')->where('user_id', '=', $request->user_id)->sum('sum');
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

        $sum = DB::table('balances')->select('sum')->where('user_id', '=', $request->user_id)->sum('sum');
        if ($sum >= $request->sum) {

            $data1 = new Balance();
            $data1->user_id = $request->user_id;
            $data1->sum = $request->sum * -1;
            $data1->status = 'cashout';
            $data1->user_id_transfer = $request->user_id_transfer;
            $data1->save();

            $data2 = new Balance();
            $data2->user_id = $request->user_id_transfer;
            $data2->sum = $request->sum;
            $data2->status = 'cashin';
            $data2->user_id_transfer = $request->user_id;
            $data2->save();
            
            /*
            $data = DB::table('balances')->insert([
                //списание
                ['user_id' => $request->user_id, 'sum' => $request->sum * -1, "status" => 'transfer_cashout', "user_id_transfer" => $request->user_id_transfer],
                //зачисление
                ['user_id' => $request->user_id_transfer, 'sum' => $request->sum, "status" => 'transfer_cashin', "user_id_transfer" => $request->user_id]
            ]);
            */

            return response()->json([
                'status' => true,
                'data1' => $data1,
                'data2' => $data2
            ], 201);
        }
    }

    //получение баланса
    public function getBalance(Request $request) {

        if (DB::table('balances')->where('user_id', '=', $request->user_id)->exists()) {

            $data = DB::table('balances')->select('sum')->where('user_id', '=', $request->user_id)->sum('sum');

            return response()->json([
                'status' => true,
                'balance' => $data
            ], 200);
        }
    }

    //Доп. задание 1
    public function currencyConverter(Request $request) {

        $access_key = 'f26397585b23d554fa4c835018242f88';
        $amount = DB::table('balances')->select('sum')->where('user_id', '=', $request->user_id)->sum('sum');

        //return 'https://api.exchangeratesapi.io/v1/latest?access_key='.$access_key.'&base=RUB&symbols='.$request->currency.'';

        $ch = curl_init('https://api.exchangeratesapi.io/v1/latest?access_key='.$access_key.'&base=RUB&symbols='.$request->currency.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($ch);
        curl_close($ch);
        
        $return = json_decode($json, true);
        return $return;

        $amount = $amount * $return->rates[$request->currency];

        return response()->json([
            'status' => true,
            'balance' => $amount,
            'currency' => $request->currency
        ], 200);
    }

    //Доп. задание 2
    public function listTransactions(Request $request) {

        $query = DB::table('balances')->select('user_id', 'sum', 'status', 'user_id_transfer');
        $query->where('user_id', '=', $request->user_id);
        
        if (!empty($request->orderSum)) {
            $query->orderBy('sum', 'DESC');
        }        
        if (!empty($request->orderDate)) {
            $query->orderBy('created_at', 'DESC');
        }      
        if (!empty($request->filterDate)) {
            $query->whereDate('created_at', '=', $request->created_at);
        }  
        //dump($query->toSql());
        
        $data = $query->paginate(3)->withQueryString();
        Paginator::useBootstrap();

        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        // GET from URL
        $id = $request->input('id');
        $limit = $request->input('limit');
        $status = $request->input('status');

        // Detail by Id / Spesifik item
        if ($id)
        {
            $transaction = Transaction::with(['items.product'])->find($id);

            if ($transaction) 
            {
                return ResponseFormatter::success(
                    $transaction,
                    "Data Transaksi Berhasil Diambil"
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    "Data Transaksi Tidak Ada"
                );
            }
        }

        $transaction = Transaction::with(['items.product'])->where('users_id', Auth::user()->id);

        if ($status)
        {
            $transaction->where('status', $status);
        }

        return ResponseFormatter::success(
            $transaction->paginate($limit), 
            "Data List Transaksi Berhasil Diambil"
        );
    }
}

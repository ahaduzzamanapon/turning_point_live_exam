<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Ensure wallet exists
        $wallet = $user->wallet()->firstOrCreate([]);

        $transactions = $wallet->transactions()->latest()->paginate(10);

        return view('student.wallet.index', compact('wallet', 'transactions'));
    }
}

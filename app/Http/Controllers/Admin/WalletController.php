<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        // Users with their wallets, paginate
        $users = User::with('wallet')->latest()->paginate(10);
        return view('admin.wallet.index', compact('users'));
    }

    public function addMoney(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string'
        ]);

        $user = User::find($request->user_id);

        // Ensure user has a wallet
        $wallet = $user->wallet()->firstOrCreate([]);

        $wallet->deposit($request->amount, $request->note ?? 'Admin Deposit', auth()->id());

        return redirect()->back()->with('success', 'Money added successfully to ' . $user->name . '\'s wallet.');
    }
}

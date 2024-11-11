<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Investment;
use Illuminate\Support\Facades\Auth;

class LenderController extends Controller
{
    public function index()
    {
        return view('lender.index');
    }

    public function getInvestmentData()
    {
        $user = Auth::user();
        $totalInvestment = Investment::where('user_id', $user->id)->sum('amount');
        $investmentHistory = Investment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'user' => $user,
            'total_investment' => $totalInvestment,
            'investment_history' => $investmentHistory
        ]);
    }

    public function createInvestment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100000',
            'bank_code' => 'required|in:1123,1124,1125,1126'
        ]);

        $user = Auth::user();
        $virtualAccount = $request->bank_code . str_replace('+62', '', $user->phone);

        $investment = Investment::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'bank_code' => $request->bank_code,
            'virtual_account' => $virtualAccount,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Investment created successfully',
            'investment' => $investment
        ]);
    }
}

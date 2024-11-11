<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BorrowerController extends Controller
{
    public function index()
    {
        return view('borrower.index');
    }

    public function getLoanLimit()
    {
        $user = Auth::user();
        $loanLimit = $user->monthly_income * 0.3;

        return response()->json([
            'monthly_income' => $user->monthly_income,
            'loan_limit' => $loanLimit,
            'user' => $user
        ]);
    }
}

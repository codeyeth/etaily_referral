<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\AccountDetails;
use App\Models\Transactions;

class HomeController extends Controller
{
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function index() {
        $findAccountDetails = AccountDetails::where('user_id', Auth::user()->id)->get();
        $transactionLogs = Transactions::where('user_id', Auth::user()->id)->get();

        $accountDetails = null;
        if(count($findAccountDetails) == 1){
            $accountDetails = $findAccountDetails[0];
        }

        return view('home')
        ->with('account_details', $accountDetails)
        ->with('transaction_logs', $transactionLogs);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Referrals;
use App\Models\AccountDetails;
use App\Models\Transactions;
use Auth;

class AccountActionController extends Controller {
    public function activate_account(Request $request){
        $is_referred = false;
        $referred_by_id = null;
        $referred_by_name = null;

        if($request->referral_code != ''){
            // find who the referral owner is
            $findReferral = AccountDetails::where('referral_code', $request->referral_code)->first();
            if ($findReferral) {
                $findUser = User::find($findReferral->user_id);
                $is_referred = true;
                $referred_by_id = $findUser->id;
                $referred_by_name = $findUser->name;

                $findReferral->update([
                    'have_pending_bonus' => true
                ]);

                // save the referral used
                $logReferral = new Referrals();
                $logReferral->referral_code_used = $request->referral_code;
                $logReferral->user_id = Auth::user()->id;
                $logReferral->user_name = Auth::user()->name;
                $logReferral->save();
            }else{
                return response()->json(['status' => 'Code not found!', 'type' => 'error']);
                return false;
            }
        }

        $findUser = User::find($request->id);
        $findUser->is_activated = true;
        $findUser->update();

        $saveAccount = new AccountDetails();
        $saveAccount->user_id = $findUser->id;
        $saveAccount->referral_code =  Str::random(6);
        $saveAccount->account_balance = 0;
        $saveAccount->is_referred = $is_referred;
        $saveAccount->referred_by_id = $referred_by_id;
        $saveAccount->referred_by_name = $referred_by_name;
        $saveAccount->save();

        $this->logTransaction('Activate Account', Auth::user());

        return response()->json(['status' => 'Account activated!', 'type' => 'success']);
    }

    public function deposit_account(Request $request){
        $findUser = User::find($request->id);
        $accountDetails = AccountDetails::where('user_id', $findUser->id)->first();

        if ($accountDetails) {
            if($accountDetails->have_pending_bonus){
                $this->logTransaction('P250 Bonus credited to Account', Auth::user(), $request->amount);
                $currentBalance = $accountDetails->account_balance + 250;
            }else{
                $currentBalance = $accountDetails->account_balance;
            }
            $updatedBalance = $currentBalance + $request->amount;

            $accountDetails->update([
                'have_pending_bonus' => false,
                'account_balance' => $updatedBalance
            ]);

            $this->logTransaction('Deposit to Account', Auth::user(), $request->amount);



            return response()->json(['status' => 'Account deposited!', 'type' => 'success', 'amount' => $request->amount]);
        }
        return response()->json(['status' => 'Account not found!', 'type' => 'error']);
    }

    public function logTransaction($action, $user, $depositAmount = null, $withdrawalAmount = null){
        $saveAccount = new Transactions();
        $saveAccount->action = $action;
        $saveAccount->user_id = $user->id;
        $saveAccount->user_name = $user->name;
        $saveAccount->deposit_amount = $depositAmount;
        $saveAccount->withdrawal_amount = $withdrawalAmount;
        $saveAccount->save();
    }
}

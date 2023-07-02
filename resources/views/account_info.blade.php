<div class="card mb-4">
    <div class="card-header">
        Account Information
    </div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            <li class="list-group-item">Account: <b> {{ Auth::user()->name }} </b></li>
            @if(Auth::user()->is_activated && $account_details != null)
            <li class="list-group-item">Available Balance: <b>{{ number_format($account_details->account_balance, 2) }}</b></li>
            <li class="list-group-item">Referral Code: <b>{{ $account_details->referral_code }}</b></li>
            <li class="list-group-item">Account since: <b>{{ Carbon\Carbon::parse($account_details->created_at)->format('M d, Y H:i A') }}</b></li>
            <li class="list-group-item">Last login: <b>{{ Carbon\Carbon::parse(Auth::user()->last_login_at)->format('M d, Y H:i A') }}</b></li>
            @endif
        </ul>
    </div>
</div>

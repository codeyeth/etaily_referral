@if($item->action === 'Deposit to Account')
<li><b class="fs-5"> {{ $item->action }} - {{ number_format($item->deposit_amount, 2) }}</b> <i> ... {{ Carbon\Carbon::parse($item->created_at)->format('M d, Y H:i A') }}</i></li>
@elseif($item->action === 'Withdraw from Account')

@else
<li><b class="fs-5"> {{ $item->action }}</b> <i> ... {{ Carbon\Carbon::parse($item->created_at)->format('M d, Y H:i A') }}</i></li>
@endif

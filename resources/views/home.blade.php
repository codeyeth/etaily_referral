@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="display-6 mb-3">Dashboard</div>

            @if (session('status'))
            <div class="alert {{session('type') == 'success' ? 'alert-success':'alert-danger'}} alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="row d-flex justify-content-between g-4">
                <div class="col-md-12 col-lg-4">
                    @include('account_info')
                    @if(!Auth::user()->is_activated)
                    <div class="card border-0 mb-2">
                        {{-- <a class="btn btn-success" href="{{ route('account.activate', Auth::user()->id) }}">Activate Account</a> --}}
                        <button class="btn btn-success" id="activate" name="activate">Activate Account</button>
                    </div>
                    @else
                    <div class="card border-0 mb-2">
                        <button class="btn btn-primary" id="deposit" name="deposit">Deposit</button>
                    </div>
                    @endif
                </div>

                <div class="col-md-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            Transactions
                        </div>
                        <div class="card-body">
                            @if ($transaction_logs->isNotEmpty())
                            <ol>
                                @foreach ($transaction_logs as $item)
                                @include('transactions')
                                @endforeach
                            </ol>
                            @else
                            <p>No records found...</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $("#deposit").click(function(){
        Swal.fire({
            title: 'Enter Amount',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            showLoaderOnConfirm: true,
            preConfirm: (amount) => {
                if(amount.length){
                    const id = {{ Auth::user()->id }};
                    const data = {
                        id: id,
                        amount: amount,
                    };
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const options = {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(data)
                    };
                    const url = "/account-deposit";
                    return fetch(url, options)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                        `Request failed: ${error}`
                        )
                    })
                }else{
                    return false;
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                'Deposit Success!',
                result.value.amount + ' is credited to your Account.',
                'success'
                )
            }
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        })
    });

    $("#activate").click(function(){
        Swal.fire({
            title: 'Enter referral code if any. Leave blank if none.',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Proceed',
            showLoaderOnConfirm: true,
            preConfirm: (referral_code) => {
                const id = {{ Auth::user()->id }};
                const data = {
                    id: id,
                    referral_code: referral_code,
                };
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const options = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                };
                const url = "/account-activate";
                return fetch(url, options)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.statusText)
                    }
                    return response.json()
                })
                .catch(error => {
                    Swal.showValidationMessage(
                    `Request failed: ${error}`
                    )
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                result.value.status,
                '',
                result.value.type,
                )
            }
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        })
    });
</script>
@endpush
@endsection

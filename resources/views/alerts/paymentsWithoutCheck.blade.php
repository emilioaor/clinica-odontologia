@if(Auth::user()->isAdmin())
    @if(($paymentsWithoutCheck = \App\Payment::paymentsWithoutCheck())['payments'])
        <div class="container">
            <div class="alert alert-info alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4>
                    Existen pagos sin revisar
                    @if($paymentsWithoutCheck['end'] > $paymentsWithoutCheck['start'])
                        desde el <strong>{{ $paymentsWithoutCheck['start']->format('m/d/Y') }}</strong>
                        hasta <strong>{{ $paymentsWithoutCheck['end']->format('m/d/Y') }}</strong>
                    @else
                        el <strong>{{ $paymentsWithoutCheck['start']->format('m/d/Y') }}</strong>
                    @endif
                </h4>
            </div>
        </div>
    @endif
@endif
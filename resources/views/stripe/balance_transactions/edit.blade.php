@extends('template')
@section('content')

<div class="row bg-cover">
    <div class="col-lg-12">
        <h1>
            Process Balance Transaction ID #{{ $balance_transaction->id }}
        </h1>
    </div>
    <div class="col-lg-12">
        <a class="btn btn-light" data-toggle="collapse" href="#collapsedInstructions" role="button" aria-expanded="false" aria-controls="collapsedInstructions">
            Instructions
        </a>
    </div>
    <div class="collapse" id="collapsedInstructions">
        <div class="card card-body">
            <ol>
                @switch ($balance_transaction->transaction_type)
                @case ('Manual')
                    <li>Select the desired <strong><u>Donor</u></strong> from the Donor dropdown list and <u>click</u> on the <i>Retrieve Donor</i> button.
                    <li>If applicable, select the desired <strong><u>Retreat</u></strong> from the Retreat dropdown list.</li>
                    <li>If more than one category is associated with the balance transaction, allocate the donation appropriately.</li>
                    <li>Review the data and then <u>click</u> on the <i>Process Balance Transaction</i> button</li>
                    <li>The donation(s) and payment(s) associated with the Balance Transaction will be created.
                    @break
                @case ('Donation')
                    <li>Select the appropriate Squarespace Contribution</li>
                    <li>Click on the <i>Process Balance Transaction: Contribution</i> button</li>
                    @break
                @case ('Invoice')
                    <li>Select the desired <strong><u>Donor</u></strong> from the Donor dropdown list and <u>click</u> on the <i>Retrieve Donor Information</i> button.
                    <li>Select the desired <strong><u>Donation</u></strong> from the Donation dropdown list and <u>click</u> on the <i>Process Balance Transaction</i> button.</li>
                    <li>The recurring payment associated with the Balance Transaction added to the selected Donation. 
                    <li>Note, that if the new payment causes the amount paid to exceed the amount pledged, the amount pledged will be increased to the total amount paid.
                    @break
                @endswitch
            </ol>
        </div>
    </div>

    <div class="col-lg-12">
        {!! Form::open(['method' => 'PUT', 'route' => ['stripe.balance_transaction.update', $balance_transaction->id]]) !!}
        {!! Form::hidden('id', $balance_transaction->id) !!}
        {!! Form::hidden('payout_id', $balance_transaction->payout_id) !!}
        
        <hr>
        <div class="form-group">
            <div class="row">
                @switch ($balance_transaction->transaction_type)
                    @case ('Manual')
                        <div class="col-lg-4 col-md-6">
                            <h3>
                                @if (isset($balance_transaction->contact_id))
                                    Donor: <a href="{{url('person/'.$balance_transaction->contact_id)}}">{{$balance_transaction->name}}</a>
                                @else
                                    {!! Form::label('contact_id', 'Donor: ' .$balance_transaction->name) !!}
                                @endIf
                            </h3>
                            {!! Form::select('contact_id', $matching_contacts, (isset($balance_transaction->contact_id)) ? $balance_transaction->contact_id : 0, ['class' => 'form-control']) !!}
                            
                            <h3>
                                {!! Form::label('event_id', 'Retreat:') !!}
                            </h3>
                            {!! Form::select('event_id', $retreats, (isset($balance_transaction->event_id) ? $balance_transaction->event_id : '' ), ['class' => 'form-control']) !!}
                            
                            <hr />
                            <br /><strong>Email: </strong><a href="mailto:{{ $balance_transaction->email }}">{{ $balance_transaction->email }}</a>
                            <br /><strong>Zip: </strong>{{ $balance_transaction->zip }}
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <h3>
                                Total Amount: ${{ number_format($balance_transaction->total_amount,2) }}
                            </h3>                    
                            @if (is_array($transaction_types))
                                @foreach ($transaction_types as $type)
                                    {!! Form::label(str_replace(" ", "_",strtolower($type)), $type) !!}</h3>
                                    @if (count($transaction_types) == 1)
                                        {!! Form::number(str_replace(" ", "_",strtolower($type)), $balance_transaction->total_amount, ['class' => 'form-control']) !!}
                                    @else
                                        {!! Form::number(str_replace(" ", "_",strtolower($type)), null, ['class' => 'form-control']) !!}
                                    @endIf
                                @endForeach
                            @else
                                {!! Form::label(str_replace(" ","_", strtolower($transaction_types)), $transaction_types) !!}</h3>
                                {!! Form::number(str_replace(" ","_", strtolower($transaction_types)), $balance_transaction->total_amount, ['class' => 'form-control']) !!}
                            @endIf
                            <hr />
                            <strong>Fee: </strong> ${{ number_format($balance_transaction->fee_amount,2) }}
                            <br /><strong>Net Amount: </strong> ${{ number_format($balance_transaction->net_amount,2) }}
                            <br /><strong>Credit Card Last 4: </strong>{{ $balance_transaction->cc_last_4 }}
                        </div>

                        <div class='col-lg-4 col-md-6'>
                            <strong>Payout Date: </strong>{{ (isset($balance_transaction->payout_date)) ? $balance_transaction->payout_date->format('m-d-Y') : null }}
                            <br /><strong>Available Date: </strong>{{ (isset($balance_transaction->available_date)) ? $balance_transaction->available_date->format('m-d-Y') : null }}
                        </div>
                    @break
                @case ('Donation')
                    <div class="col-lg-4 col-md-6">
                        <h3>
                            {!! Form::label('contribution_id', 'Contribution for: ' .$balance_transaction->name) . ' - $' . number_format($balance_transaction->total_amount,2) . ' - ' . $balance_transaction->payout_date->format('m-d-Y') !!}</h3>
                            {!! Form::select('contribution_id', $unprocessed_squarespace_contributions, null, ['class' => 'form-control']) !!}
                        </h3>
                    </div>
                    @break
                @case ('Invoice')
                    <div class="col-lg-4 col-md-6">
                        <h3>
                            @if (isset($balance_transaction->contact_id))
                                Donor: <a href="{{url('person/'.$balance_transaction->contact_id)}}">{{$balance_transaction->name}}</a>
                            @else
                                {!! Form::label('contact_id', 'Donor: ' .$balance_transaction->name) !!}
                            @endIf
                        </h3>
                        {!! Form::select('contact_id', $matching_contacts, (isset($balance_transaction->contact_id)) ? $balance_transaction->contact_id : 0, ['class' => 'form-control']) !!}
                    </div>

                    <div class="col-lg-4 col-md-6">
                        
                        <h3>
                            {!! Form::label('donation_id', 'Donation:') !!}
                        </h3>
                        {!! Form::select('donation_id', $matching_contacts, (isset($balance_transaction->event_id) ? $balance_transaction->event_id : '' ), ['class' => 'form-control']) !!}
                        
                        <hr />
                        <strong>Email: </strong><a href="mailto:{{ $balance_transaction->email }}">{{ $balance_transaction->email }}</a>
                        <br /><strong>Zip: </strong>{{ $balance_transaction->zip }}
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <h3>
                            Total Amount: ${{ number_format($balance_transaction->total_amount,2) }}
                        </h3>                    
                        <hr />
                        <strong>Fee: </strong> ${{ number_format($balance_transaction->fee_amount,2) }}
                        <br /><strong>Net Amount: </strong> ${{ number_format($balance_transaction->net_amount,2) }}
                        <br /><strong>Credit Card Last 4: </strong>{{ $balance_transaction->cc_last_4 }}                       
                        <hr>
                        <strong>Payout Date: </strong>{{ (isset($balance_transaction->payout_date)) ? $balance_transaction->payout_date->format('m-d-Y') : null }}
                        <br /><strong>Available Date: </strong>{{ (isset($balance_transaction->available_date)) ? $balance_transaction->available_date->format('m-d-Y') : null }}
                    </div>
                    @break

                @endSwitch
            </div>  
        
            <div class="row text-center mt-3">
                <div class='col-lg-12'>
                    @if (!isset($balance_transaction->reconciled))
                        @switch ($balance_transaction->transaction_type)
                            @case ('Manual')
                                @if ($balance_transaction->contact_id > 0)
                                    {!! Form::submit('Process Balance Transaction',['class' => 'btn btn-dark']) !!}
                                @else
                                    {!! Form::submit('Retrieve Contact Info',['class' => 'btn btn-info']) !!}
                                @endif
                                @break
                            @case('Donation')
                                {!! Form::submit('Process Balance Transaction: Contribution',['class' => 'btn btn-dark']) !!}
                                @break
                            @case ('Invoice')
                                @if ($balance_transaction->contact_id > 0)
                                    {!! Form::submit('Process Balance Transaction',['class' => 'btn btn-dark']) !!}
                                @else
                                    {!! Form::submit('Retrieve Donor Information',['class' => 'btn btn-info']) !!}
                                @endif
                                @break
                        @endswitch
                    @else
                        <a class="btn btn-primary" href="{{ action([\App\Http\Controllers\StripeBalanceTransactionController::class, 'index']) }}">Balance Transaction #{{ $balance_transaction->id }} has already been processed</a>
                    @endIf
                </div>
            </div>
            
            <hr>            
            
            <div class="row">
                <div class='col-lg-6 col-md-8'>
                    <strong>Payout ID: </strong><a href="{{URL('stripe/payout/'.$balance_transaction->payout_id)}}"> {{$balance_transaction->payout_id}} </a>
                    <br /><strong>Balance Transaction ID: </strong><a href="{{URL('stripe/balance_transaction/'.$balance_transaction->balance_transaction_id)}}"> {{ $balance_transaction->balance_transaction_id }} </a>
                    <br /><strong>Charge ID: </strong><a href="{{URL('stripe/charge/'.$balance_transaction->charge_id)}}"> {{$balance_transaction->charge_id}}</a>
                    <br /><strong>Customer ID: </strong>{{$balance_transaction->customer_id}}
                </div>
            </div>
            
        </div>
        {!! Form::close() !!}
    </div>
</div>
@stop

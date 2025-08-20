@extends('layouts.master')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-dashboard"></i> Manage Income</h1>
        <p>Start a beautiful journey here</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="#">Blank Page</a></li>
    </ul>
</div>
    <div id="addThisFormContainer">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>New Income</h3>
                        <div class="ermsg"> </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">

                                        {!! Form::open(['url' => 'income/create','id'=>'createThisForm']) !!}
                                        {!! Form::hidden('incomeid','', ['id' => 'incomeid']) !!}
                                        {!! Form::label('Title', 'Date', ['class' => 'awesome']) !!}
                                        {!! Form::date('title','',['id'=>'income_date','class'=>'form-control','placeholder'=>'Date']) !!}

                                    </div>
                                    <div class="col-md-4">
                                        <div>
                                            <label for="account_id" class="awesome">Account</label>
                                            <select name="account_id" class="form-control" id="account_id" required>
                                                <option value="" selected>Please Select</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{$account->id}}">{{ $account->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div>
                                            <label for="ref">Ref</label>
                                            <input type="text" id="ref" name="ref" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <label for="transaction_type" class="awesome">Transaction Type</label>
                                    <select name="transaction_type" class="form-control" id="transaction_type" required>
                                        <option value="Current" >Current</option>
                                        <option value="Advance" >Advance</option>
                                        <option value="Due" >Due</option>
                                        <option value="Refund">Refund</option>
                                        <option value="Advance Adjust" >Advance Adjust</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="amount">Amount</label>
                                    <input type="number" id="amount" name="amount" class="form-control" required>
                                </div>
                                <div id="vatid">
                                <div>
                                    <label for="tax_rate">Vat(%)</label>
                                    <input type="number" id="tax_rate" name="tax_rate" class="form-control" >
                                </div>
                                <div>
                                    <label for="tax_amount">Vat Amount</label>
                                    <input type="number"  id="tax_amount" name="tax_amount" class="form-control" step="any" min="0">
                                </div>
                                
                                <div>
                                    <label for="after_tax_amount">Total Amount</label>
                                    <input type="number" id="after_tax_amount"  name="after_tax_amount" class="form-control">
                                    <input type="hidden" id="returned_change" name="returned_change" value="0" />
                                </div>
                                </div>
                                
                                <div id="payment_div">
                                    <label for="payment_type" class="awesome">Payment Type</label>
                                    <select name="payment_type" class="form-control" id="payment_type" required>
                                        <option value="" selected>Please Select</option>
                                        <option value="Cash" >Cash</option>
                                        <option value="Bank" >Bank</option>
                                    </select>
                                </div>
                                <div id="showreceivable">
                                    <label for="asset_id" class="awesome">Account Receivable Holder</label>
                                    <select name="asset_id" class="form-control" id="asset_id">
                                        <option value="" selected>Please Select</option>
                                        @foreach($receivables as $receivable)
                                            <option value="{{ $receivable->id }}">{{ $receivable->account_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {!! Form::label('Description', 'Description', ['class' => 'awesome']) !!}
                                {!! Form::text('description','',['id'=>'description','class'=>'form-control','placeholder'=>'Description']) !!}

                                <hr>
                                <input type="button" id="addBtn" value="Create" class="btn btn-primary">
                                <input type="button" id="FormCloseBtn" value="Close" class="btn btn-warning">
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
    <button id="newBtn" type="button" class="btn btn-info">Add New Income</button>
    <hr>
 @endif    
    <div id="contentContainer">


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3> Income Details</h3>
                        
                        <form  action="{{route('income_search')}}" method ="POST">
                            @csrf
                            <br>
                            <div class="container">
                                <div class="row">
                                    <div class="container-fluid">
                                        <div class="form-group row">
                                            <label for="date" class="col-form-label col-md-2">From Date</label>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" id="fromDate" name="fromDate" required/>
                                            </div>
                                            <label for="date" class="col-form-label col-md-2">To Date</label>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" id="toDate" name="toDate" required/>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn" name="search" title="Search"><img src="https://img.icons8.com/android/24/000000/search.png"/></button>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>
                            <br>
                        </form>
                        
                    </div>
                    <div class="toppad">
                        <div class="row">
                            <div class="container">
                    <div class="text-center">
                    <h1>Branch: {{(auth()->user()->branch->branch_name) }}</h1>
                    <h5>Phone: {{(auth()->user()->branch->branch_phone) }}</h5>
                    <p>{{(auth()->user()->branch->branch_address) }}</p>
                    <h5>Income Details</h5>
                     <?php 
                            if(isset($pdfhead["title"])){
                                $title = $pdfhead["title"];
                            }else {
                                $title = "Generated Report";
                            }
                            if(isset($pdfhead["fromDate"]) && $pdfhead["fromDate"]!="" ){
                                $fromDate = $pdfhead["fromDate"]." to ";
                            }else{
                                $fromDate = "All Data";
                            }
                            if(isset($pdfhead["toDate"])){
                                $toDate = $pdfhead["toDate"];
                            }else{
                                $toDate = "";
                            }
                            ?>

                 <h3>Data: {{$fromDate.' '.$toDate}} </h3>                      
                </div>

        <table class="table table-bordered table-hover" id="example">
            <thead>
            <tr>
                <th>Date</th>
                <th>Account</th>
                <th>Ref</th>
                <th>Description</th>
                <th>Transaction Type</th>
                <th>Payment Type</th>
                <th>Gross Amount</th>
                <th>Vat Rate</th>
                <th>Vat Amount</th>
                <th>Net Amount</th>
                @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                <th>Action</th>
                @endif

            </tr>
            </thead>
            <tbody>
            @forelse ($trans as $tran)
                <tr>
                    <td>{{$tran->t_date}}</td>
                    <td>{{$tran->account->account_name}}</td>
                    <td>{{$tran->ref}}</td>
                    <td>{{$tran->description}}</td>
                    <td>{{$tran->transaction_type}}</td>
                    <td>{{$tran->payment_type}}</td>
                    <td>{{$tran->amount}}</td>
                    <td>{{$tran->t_rate}}</td>
                    <td>{{$tran->t_amount}}</td>
                    <td>{{$tran->at_amount}}</td>
                    @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                    <td>
                        <a id="EditBtn" rid="{{$tran->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                         @if (auth()->user()->user_type == 11)
                        <a id="deleteBtn" rid="{{$tran->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                         @endif
                        <a href="#" rid="{{$tran->id}}" data-toggle="modal" data-target="#assignSubcat" class="trandiagnosis"><i class="fa fa-eye" style="color: #5cb85c;font-size:16px;"></i></a>
                    </td>
                    @endif
                </tr>
            @empty
                <h3>No post found from you. Create a new Income</h3>
            @endforelse
            </tbody>
        </table>
{{--        {{$incomes->links()}}--}}
        {{--        {{$branches->links()}}--}}



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {

            $("#addThisFormContainer").hide();
            $("#newBtn").click(function(){
                clearform();
                $("#newBtn").hide(100);
                $("#addThisFormContainer").show(300);

            });
            $("#FormCloseBtn").click(function(){
                $("#addThisFormContainer").hide(200);
                $("#newBtn").show(100);
                clearform();
            });

            // calculation start 
            $("#amount, #tax_rate").keyup(function(){
                var total=0;
                var amount = Number($("#amount").val());
                var taxrate = Number($("#tax_rate").val());
                var after_tax_amount = amount / (1+(taxrate/100));
                var tax_amount = amount - after_tax_amount;
                if(taxrate == ''){
                    $('#tax_amount').val('');
                    $('#after_tax_amount').val(amount);
                }else{
                $('#tax_amount').val(tax_amount.toFixed(2));
                $('#after_tax_amount').val(after_tax_amount.toFixed(2));
                }
            });
            //calculation end
            
            
            
            
            //calculation end

            $("#amount, #tax_amount").keyup(function(){
                var total=0;
                var amount = parseFloat($("#amount").val()) || 0;
                var tax_amount = parseFloat($("#tax_amount").val()) || 0;
                var after_tax_amount = amount - tax_amount;
                
                $('#after_tax_amount').val(after_tax_amount.toFixed(2));
            });
            //calculation end

            
            

            $("#payment_type").change(function(){
                $(this).find("option:selected").each(function(){
                    var val = $(this).val();

                    if( val == "Account Receivable" ){
                        $("#showreceivable").show();
                    } else{
                        $("#showreceivable").hide();
                    }
                });
            }).change();



            $("#transaction_type").change(function(){
                $(this).find("option:selected").each(function(){
                    var val = $(this).val();

                    if( val == "Advance Adjust" ){
                        $("#payment_div").hide();
                        $("#vatid").hide();
                        $("#showreceivable").hide();
                    }
                    else if(val == "Refund" ){
                        $("#payment_div").show();
                        $("#vatid").hide();

                    }else{
                        $("#payment_div").show();
                        $("#vatid").show();
                    }
                });
            }).change();

            $("#transaction_type").change(function () {

                var transaction_type = $(this).val();
                if (transaction_type == "Due") {
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Account Receivable'>Account Receivable</option>");
                } else if (transaction_type == "Current") {
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                } else if (transaction_type == "Advance") {
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                }else if (transaction_type == "Advance Adjust") {
                    clearTaxPaymentTypefield()
                    $("#payment_type").html("<option value=''>Please Select</option>");
                }
            });


            //header for csrf-token is must in laravel
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            //

            var url = "{{URL::to('/income')}}";
            $("#addBtn").click(function(){
                if($(this).val() == 'Create') {
                    var income_date= $("#income_date").val();
                    var account_id= $("#account_id").val();
                    var ref= $("#ref").val();
                    var amount= $("#amount").val();
                    var tax_rate= $("#tax_rate").val();
                    var tax_amount= $("#tax_amount").val();
                    var after_tax_amount= $("#after_tax_amount").val();
                    var transaction_type= $("#transaction_type").val();
                    var payment_type= $("#payment_type").val();
                    var asset_id= $("#asset_id").val();
                    var description= $("#description").val();

                    console.log(income_date + account_id + ref + amount + tax_rate + tax_amount + after_tax_amount + transaction_type+ payment_type + asset_id + description);

                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {income_date:income_date,account_id:account_id,ref:ref,amount:amount,tax_rate:tax_rate,tax_amount:tax_amount,after_tax_amount:after_tax_amount,transaction_type:transaction_type,payment_type:payment_type,asset_id:asset_id,description:description},
                        success: function (d) {
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                                pagetop();
                            }else if(d.status == 300){
                                $(".ermsg").html(d.message);
                                pagetop();
                                window.setTimeout(function(){location.reload()},2000)
                            }
                        },
                        error: function (d) {
                            console.log(d);
                        }
                    });
                }

                //create  end
                //Update Branch
                if($(this).val() == 'Update'){
                    $.ajax({
                        url:url+'/'+$("#incomeid").val(),
                        method: "PUT",
                        type: "PUT",
                        data:{
                            income_date: $("#income_date").val(),
                            account_id: $("#account_id").val(),
                            ref: $("#ref").val(),
                            amount: $("#amount").val(),
                            tax_rate: $("#tax_rate").val(),
                            tax_amount: $("#tax_amount").val(),
                            after_tax_amount: $("#after_tax_amount").val(),
                            transaction_type: $("#transaction_type").val(),
                            payment_type: $("#payment_type").val(),
                            asset_id: $("#asset_id").val(),
                            description: $("#description").val()
                        },
                        success: function(d){
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                                pagetop();
                            }else if(d.status == 300){
                                $(".ermsg").html(d.message);
                                pagetop();
                                window.setTimeout(function(){location.reload()},2000)
                            }
                        },
                        error:function(d){
                            console.log(d);
                        }
                    });
                }
                //Update Branch
            });
            //Edit
            $("#contentContainer").on('click','#EditBtn', function(){
                $incomeid = $(this).attr('rid');
                $info_url = url + '/'+$incomeid+'/edit';
                console.log($info_url);
                $.get($info_url,{},function(d){
                    populateForm(d);
                    pagetop();
                });
            });
            //Edit Room end

            //Delete Branch
            $("#contentContainer").on('click','#deleteBtn', function(){
                //alert()
                if(!confirm('Sure?')) return;
                $incomeid = $(this).attr('rid');
                //console.log($roomid);
                $info_url = url + '/'+$incomeid;
                $.ajax({
                    url:$info_url,
                    method: "DELETE",
                    type: "DELETE",
                    data:{
                    },
                    success: function(d){
                        if(d.success) {
                            alert(d.message);
                            location.reload();
                        }
                    },
                    error:function(d){
                        console.log(d);
                    }
                });
            });
            //Delete Branch




            function populateForm(data){
                $("#income_date").val(data.t_date);
                $("#account_id").val(data.account_id);
                $("#ref").val(data.ref);
                $("#amount").val(data.amount);
                $("#tax_rate").val(data.t_rate);
                $("#tax_amount").val(data.t_amount);
                $("#after_tax_amount").val(data.at_amount);
                
                
                $("#transaction_type").val(data.transaction_type);

                if(  data.transaction_type == "Advance Adjust"){
                        $("#payment_div").hide();
                        $("#vatid").hide();
                        $("#showreceivable").hide();
                    }
                    else if(data.transaction_type == "Refund" ){
                        $("#payment_div").show();
                        $("#vatid").hide();

                    }else{
                        $("#payment_div").show();
                        $("#vatid").show();
                    }


                if (data.transaction_type == "Due") {
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Account Receivable'>Account Receivable</option>");
                } else if (data.transaction_type == "Current") {
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                } else if (data.transaction_type == "Advance") {
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                }else if (data.transaction_type == "Advance Adjust") {
                    clearTaxPaymentTypefield()
                    $("#payment_type").html("<option value=''>Please Select</option>");
                }


                
                $("#payment_type").val(data.payment_type);
                $("#asset_id").val(data.asset_id);
                $("#description").val(data.description);
                $("#incomeid").val(data.id);
                $("#addBtn").val('Update');
                $("#addThisFormContainer").show(300);
                $("#newBtn").hide(100);
            }
            function clearform(){
                $('#createThisForm')[0].reset();
                $("#addBtn").val('Create');
            }
            
            function clearTaxPaymentTypefield(){
                var amount = Number($("#amount").val());
                $('#payment_type').val('');
                $('#tax_rate').val('');
                $('#tax_amount').val('');
                $('#after_tax_amount').val(amount);
            }


        });


    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#income").addClass('active');
        });
    </script>
@endsection
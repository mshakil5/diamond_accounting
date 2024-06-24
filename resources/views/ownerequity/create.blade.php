@extends('layouts.master')



@section('content')
    <div id="addThisFormContainer">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>New Ownerequity</h3>
                        <div class="ermsg">


                            </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">

                                        {!! Form::open(['url' => 'ownerequity/create','id'=>'createThisForm']) !!}
                                        {!! Form::hidden('ownerequityid','', ['id' => 'ownerequityid']) !!}
                                        {!! Form::label('Title', 'Date', ['class' => 'awesome']) !!}
                                        {!! Form::date('title','',['id'=>'equity_date','class'=>'form-control','placeholder'=>'Date']) !!}

                                    </div>

                                    <div class="col-md-4">

                                        <div>
                                            <label for="account_id" class="awesome">Account</label>
                                            <select name="account_id" class="form-control" id="account_id">
                                                <option value="" selected>Please Select</option>
                                                @foreach($oe as $account)
                                                    <option value='{{ $account->id }}|{{ $account->account_name }}|{{ $account->account_type }}'>{{ $account->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div>
                                            <label for="ref">Ref</label>
                                            <input type="text" id="ref" name="ref" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">


                                <div>
                                    <label for="transaction_type" class="awesome">Transaction Type</label>
                                    <select name="transaction_type" class="form-control" id="transaction_type">
                                        <option value="" selected>Please Select</option>
                                    </select>
                                </div>

                                
                                <div>
                                    <label for="shareholder_id" class="awesome">Shareholder</label>
                                    <select name="shareholder_id" class="form-control" id="shareholder_id">
                                        <option value="" selected>Please Select</option>
                                        @foreach ($shareholders as $shareholder)
                                        <option value="{{$shareholder->id}}"> {{$shareholder->name}} </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div>
                                    <label for="amount">Amount</label>
                                    <input type="number" id="amount" name="amount" class="form-control" >
                                </div>
                                <div id="payment_div">
                                    <label for="payment_type" class="awesome">Payment Type</label>
                                    <select name="payment_type" class="form-control" id="payment_type">
                                        <option value="" selected>Please Select</option>
                                        <option value="Cash" >Cash</option>
                                        <option value="Bank" >Bank</option>
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
    <button id="newBtn" type="button" class="btn btn-info">Add New Ownerequity</button>
    <hr>
 @endif     
    <div id="contentContainer">


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3> Owner Equity Details</h3>

                        <form  action="{{route('oe_search')}}" method ="POST">
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
                    <div class="card-body">
                        <div class="row">
                            <div class="container">

                                <div class="text-center">
                                    <h1>Branch: {{(auth()->user()->branch->branch_name) }}</h1>
                                    <h5>Phone: {{(auth()->user()->branch->branch_phone) }}</h5>
                                    <p>{{(auth()->user()->branch->branch_address) }}</p>
                                    <h5>Owner Equity Details</h5>

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
            
                                    <h3>Data: {{$fromDate.' '.$toDate}}</h3>                                       
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
                                        <th>Amount</th>
                                        @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($ownerequities as $oe)
                                        <tr>
                                            <td>{{$oe->t_date}}</td>
                                            <td>{{$oe->account->account_name}}</td>
                                            <td>{{$oe->ref}}</td>
                                            <td>{{$oe->description}}</td>
                                            <td>{{$oe->transaction_type}}</td>
                                            <td>{{$oe->payment_type}}</td>
                                            <td>{{$oe->at_amount}}</td>
                                            
                                            @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                                <td>
                                                <a id="EditBtn" rid="{{$oe->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                                @if (auth()->user()->user_type == 11)
                                                <a id="deleteBtn" rid="{{$oe->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                                @endif
                                                <a href="#" rid="{{$oe->id}}" data-toggle="modal" data-target="#assignSubcat" class="trandiagnosis"><i class="fa fa-eye" style="color: #5cb85c;font-size:16px;"></i></a>
                                                </td>
                                            @endif
                                            
                                        </tr>
                                    @empty
                                        <h3>No data found. Create a new Owners Equity</h3>
                                    @endforelse
                                    </tbody>
                                </table>
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



             $("#account_id").change(function () {

                var i = $(this).val();
                values=i.split('|');
                account_id=values[0];
                account_name=values[1];
                account_type=values[2];

                if (account_name == "Capital") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Receive'>Receive</option>");
                } else if (account_name == "Withdraw") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Payment'>Payment</option>");
                }else if (account_name == "Revenue Reserve") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Add'>Add</option><option value='Reverse'>Reverse</option>");
                }else if (account_name == "Capital Reserve") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Add'>Add</option><option value='Reverse'>Reverse</option>");
                }else if (account_type == "Dividend") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Payment'>Payment</option><option value='Payable'>Payable</option>");
                }else if (account_name == "Share premium") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Receive'>Receive</option>");
                }
            });








            $("#transaction_type").change(function(){
                $(this).find("option:selected").each(function(){
                    var val = $(this).val();
                    if( val == "Add" || val == "Payable" || val == "Reverse"){
                        clearPaymentTypefield();
                        $("#payment_div").hide();
                    } else{
                        $("#payment_div").show();
                    }
                });
            }).change();






            //header for csrf-token is must in laravel
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            //

            var url = "{{URL::to('/ownerequity')}}";
            $("#addBtn").click(function(){
                if($(this).val() == 'Create') {
                    // alert("form work");
                    var equity_date= $("#equity_date").val();
                    var ref= $("#ref").val();
                    var amount= $("#amount").val();
                    var transaction_type= $("#transaction_type").val();
                    var shareholder_id= $("#shareholder_id").val();


                    var i = $("#account_id").val();
                        values=i.split('|');
                        account_id=values[0];
                        account_type=values[1];

                    var payment_type= $("#payment_type").val();
                    var description= $("#description").val();
                    // console.log(equity_date +','+ account_id + ','+ref  +','+ amount  +','+ transaction_type  + ','+amount_type +  ','+payment_type + ','+description);
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {equity_date:equity_date,account_id:account_id,ref:ref,amount:amount,transaction_type:transaction_type,payment_type:payment_type,description:description,shareholder_id:shareholder_id},
                        success: function (d) {
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                            }else if(d.status == 300){
                                $(".ermsg").html(d.message);
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
                    var i = $("#account_id").val();
                    values=i.split('|');
                    account_id=values[0];
                    account_name=values[1];
                    $.ajax({
                        url:url+'/'+$("#ownerequityid").val(),
                        method: "PUT",
                        type: "PUT",
                        data:{
                            equity_date: $("#equity_date").val(),
                            account_id:account_id,
                            ref: $("#ref").val(),
                            amount: $("#amount").val(),
                            shareholder_id: $("#shareholder_id").val(),
                            transaction_type: $("#transaction_type").val(),
                            amount_type: $("#amount_type").val(),
                            payment_type: $("#payment_type").val(),
                            description: $("#description").val()
                        },
                        success: function(d){
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                            }else if(d.status == 300){
                                $(".ermsg").html(d.message);
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
                $ownerequityid = $(this).attr('rid');
                $info_url = url + '/'+$ownerequityid+'/edit';
                $.get($info_url,{},function(d){
                    populateForm(d);
                    pagetop();
                });
            });
            //Edit Room end

            //Delete Branch
            $("#contentContainer").on('click','#deleteBtn', function(){
                if(!confirm('Sure?')) return;
                $ownerequityid = $(this).attr('rid');
                $info_url = url + '/'+$ownerequityid;
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
                $("#equity_date").val(data.t_date);
                var account = data.account_id+'|'+data.account_name+'|'+data.account_type;
                $("#account_id").val(account);
                // $("#account_name").val(data.account_name);
                
                if (data.account_name == "Capital") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Receive'>Receive</option>");
                } else if (data.account_name == "Withdraw") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Payment'>Payment</option>");
                }else if (data.account_name == "Revenue Reserve") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Add'>Add</option><option value='Reverse'>Reverse</option>");
                }else if (data.account_name == "Capital Reserve") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Add'>Add</option><option value='Reverse'>Reverse</option>");
                }else if (data.account_type == "Dividend") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Payment'>Payment</option><option value='Payable'>Payable</option>");
                }else if (data.account_name == "Share premium") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Receive'>Receive</option>");
                }
                
                $("#ref").val(data.ref);
                $("#shareholder_id").val(data.shareholder_id);
                $("#amount").val(data.at_amount);
                $("#transaction_type").val(data.transaction_type);
                
                if( data.transaction_type == "Add" || data.transaction_type == "Payable" || data.transaction_type == "Reverse"){
                    clearPaymentTypefield()
                    $("#payment_div").hide();
                } else{
                    $("#payment_div").show();
                }
                
                $("#amount_type").val(data.amount_type);
                $("#payment_type").val(data.payment_type);
                $("#description").val(data.description);
                $("#ownerequityid").val(data.id);
                $("#addBtn").val('Update');
                $("#addThisFormContainer").show(300);
                $("#newBtn").hide(100);
            }
            function clearform(){
                $('#createThisForm')[0].reset();
                $("#addBtn").val('Create');
            }
            
            function clearPaymentTypefield(){
                $('#payment_type').val('');
            }


        });

    </script>

    <script type="text/javascript">

        $(document).ready(function() {
            $("#allownerequity").addClass('active');
            $("#allownerequity").addClass('is-expanded');
            $("#ownerequity").addClass('active');
        });
    </script>
@endsection

@extends('layouts.master')



@section('content')


    <style>
        .due-div{
            cursor: pointer;
            cursor: underline;
            color: green;
        }
        .due-div:hover{
            text-decoration:underline;
        }
    </style>



    <div id="addThisFormContainer">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>New Liability</h3>
                         <div class="ermsg">
                              
                                
                            </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">

                                        {!! Form::open(['url' => 'liability/create','id'=>'createThisForm']) !!}
                                        {!! Form::hidden('liabilityid','', ['id' => 'liabilityid']) !!}
                                        {!! Form::label('Title', 'Date', ['class' => 'awesome']) !!}
                                        {!! Form::date('title','',['id'=>'liability_date','class'=>'form-control','placeholder'=>'Date']) !!}

                                    </div>
                                    <div class="col-md-4">
                                        <div>
                                            <label for="account_id" class="awesome">Account</label>
                                            <select name="account_id" class="form-control" id="account_id">
                                                <option value="" selected>Please Select</option>
                                                @foreach($accounts as $account)
                                                    <option value='{{ $account->id }}|{{ $account->account_type }}'>{{ $account->account_name }}</option>
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
                                    <label for="amount">Amount</label>
                                    <input type="number" id="amount" name="amount" class="form-control" >
                                </div>

                                <div id="all_payment_type">
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
    <button id="newBtn" type="button" class="btn btn-info">Add New Liability</button>
    <hr>
@endif     
    <div id="contentContainer">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3> Liability Details</h3>
                        
                        <form  action="{{route('liability_search')}}" method ="POST">
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
                                    <h5>Liability Details</h5>
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

                                <table class="table table-bordered table-hover"   id="example">
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
                                    @forelse ($liabilities as $tran)
                                        <tr>
                                            <td>{{$tran->t_date}}</td>
                                            @empty($tran->account->account_name)
                                                <td>Vat Payable</td>
                                            @else
                                                <td>{{$tran->account->account_name}}</td>
                                            @endempty

                                            <td>{{$tran->ref}}</td>
                                            <td>{{$tran->description}}</td>
                                            <td>{{$tran->transaction_type}}</td>
                                            <td>{{$tran->payment_type}}</td>
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
                                        <h3>No post found from you. Create a new Liability</h3>
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
                account_type=values[1];

                if (account_type == "Liabilities-AP") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Receive'>Receive</option><option value='Payment'>Payment</option>");
                } else if (account_type == "Long-term Liabilities") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Receive'>Receive</option><option value='Payment'>Payment</option><option value='Interest'>Interest</option>");
                } else if (account_type == "Short-term Liabilities") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Receive'>Receive</option><option value='Payment'>Payment</option>");
                } else if (account_type == "Vat Payable") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Payment'>Payment</option><option value='Add'>Add</option>");
                }
                });



            $("#transaction_type").change(function () {
                var val = $(this).val();
                if (val == "Receive") {
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                    $("#all_payment_type").show();
                } else if (val == "Payment") {
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                    $("#all_payment_type").show();
                }else if (val == "Interest") {
                    clearPaymentTypefield();
                    $("#all_payment_type").hide();
                }else if (val == "Add") {
                    clearPaymentTypefield();
                    $("#all_payment_type").hide();
                }


            });


            //header for csrf-token is must in laravel
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            //

            var url = "{{URL::to('/liability')}}";
            $("#addBtn").click(function(){
                if($(this).val() == 'Create') {
                    var liability_date= $("#liability_date").val();
                    var i = $("#account_id").val();

                    values=i.split('|');
                    account_id=values[0];
                    account_type=values[1];

                    var ref= $("#ref").val();
                    var amount= $("#amount").val();
                    var transaction_type= $("#transaction_type").val();
                    var payment_type= $("#payment_type").val();
                    var description= $("#description").val();
                    // console.log(liability_date + account_id + ref + amount+ transaction_type + payment_type + description);
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {liability_date:liability_date,account_id:account_id,account_type:account_type,ref:ref,amount:amount,transaction_type:transaction_type,payment_type:payment_type,description:description},
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
                    var i = $("#account_id").val();
                    values=i.split('|');
                    account_id=values[0];
                    account_type=values[1];
                    $.ajax({
                        url:url+'/'+$("#liabilityid").val(),
                        method: "PUT",
                        type: "PUT",
                        data:{
                            liability_date: $("#liability_date").val(),
                            account_id:account_id,
                            account_type:account_type,
                            ref: $("#ref").val(),
                            amount: $("#amount").val(),
                            transaction_type: $("#transaction_type").val(),
                            payment_type: $("#payment_type").val(),
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
                $liabilityid = $(this).attr('rid');
                $info_url = url + '/'+$liabilityid+'/edit';
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
                $liabilityid = $(this).attr('rid');
                //console.log($roomid);
                $info_url = url + '/'+$liabilityid;
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
                $("#liability_date").val(data.t_date);
                var account = data.account_id+'|'+data.account_type;
                $("#account_id").val(account);
                $("#ref").val(data.ref);
                if (data.account_type == "Liabilities-AP") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Receive'>Receive</option><option value='Payment'>Payment</option>");
                } else if (data.account_type == "Long-term Liabilities") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Receive'>Receive</option><option value='Payment'>Payment</option><option value='Interest'>Interest</option>");
                } else if (data.account_type == "Short-term Liabilities") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Receive'>Receive</option><option value='Payment'>Payment</option>");
                } else if (data.account_type == "Vat Payable") {
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Payment'>Payment</option><option value='Add'>Add</option>");
                }

                $("#transaction_type").val(data.transaction_type);
                
                if (data.transaction_type == "Receive") {
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                    $("#all_payment_type").show();
                } else if (data.transaction_type == "Initial") {
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Owner Equity'>Owner Equity</option>");
                    $("#all_payment_type").show();
                } else if (data.transaction_type == "Payment") {
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                    $("#all_payment_type").show();
                }else if (data.transaction_type == "Interest") {
                    clearPaymentTypefield();
                    $("#all_payment_type").hide();
                }else if (data.transaction_type == "Add") {
                    clearPaymentTypefield();
                    $("#all_payment_type").hide();
                }
                
                
                
                
                

                $("#amount").val(data.at_amount);
                $("#payment_type").val(data.payment_type);
                $("#description").val(data.description);
                $("#liabilityid").val(data.id);
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
            $("#liability").addClass('active');
            $("#liability").addClass('is-expanded');
            $("#addliability").addClass('active');
        });
    </script>
@endsection

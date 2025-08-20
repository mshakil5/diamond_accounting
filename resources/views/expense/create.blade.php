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
<div class="app-title">
    <div>
        <h1><i class="fa fa-dashboard"></i> Manage Expense</h1>
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
                    <h3>New Expense</h3>
                </div>
                <div class="ermsg">
                              
                                
                </div>
                <div class="card-body">
                    <div class="row">
                            

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4">

                                    {!! Form::open(['url' => 'expense/create','id'=>'createThisForm']) !!}
                                    {!! Form::hidden('expenseid','', ['id' => 'expenseid']) !!}
                                    {!! Form::label('Title', 'Date', ['class' => 'awesome']) !!}
                                    {!! Form::date('title','',['id'=>'expense_date','class'=>'form-control','placeholder'=>'Date','required' => 'required']) !!}

                                </div>
                                <div class="col-md-4">
                                    <div>
                                        <label for="account_id" class="awesome">Account</label>
                                        <select name="account_id" class="form-control" id="account_id" required>
                                            <option value="" selected>Please Select</option>
                                            @foreach($accounts as $account)
                                            @if($account->account_name != 'Salary' && $account->account_name != 'Wages')
                                                <option value='{{ $account->id }}|{{ $account->account_name }}'>{{ $account->account_name }}</option>
                                             @endif   
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
                                <input type="number"  id="tax_amount" name="tax_amount" class="form-control"  step="any" min="0">
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
                                <div id="showpayable">
                                    <label for="modelable_id" class="awesome">Payable Holder</label>
                                    <select name="modelable_id" class="form-control" id="modelable_id">
                                        <option value="" selected>Please Select</option>
                                        @foreach($payables as $payable)
                                            <option value="{{ $payable->id }}">{{ $payable->account_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- <div id="showemployee">
                                    <div id="add_due" class="due-div">
                                        <p onclick="myFunction()">+Add Due</p>
                                    </div>

                                    <div id="due_amount_div" class="resume" style="display: none;">
                                        <label for="due_amount" >Due Amount</label>
                                        <input type="number" id="due_amount" name="due_amount" class="form-control">
                                    </div>



                                    <div id="employee_div">
                                        <label for="employee_id" class="awesome">Employee</label>
                                        <select name="employee_id" class="form-control" id="employee_id">
                                            <option value="" selected>Please Select</option>
                                            @foreach($employees as $employee)
                                                <option value="{{$employee->id}}">{{ $employee->employee_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}

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
    <button id="newBtn" type="button" class="btn btn-info">Add New Expense</button>
    <hr>
 @endif    
    <div id="contentContainer">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
						{{-- search strt  --}}
                        <form  action="{{route('search')}}" method ="POST">
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
                        {{-- search end  --}}
						
                    </div>

                    <div>
            <div class="row">
            <div class="container">
                     <div class="text-center">
                    <h1>Branch: {{(auth()->user()->branch->branch_name) }}</h1>
                    <h5>Phone: {{(auth()->user()->branch->branch_phone) }}</h5>
                    <p>{{(auth()->user()->branch->branch_address) }}</p>
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
                         <h3>Report: {{$title}}</h3>
                        <h3>Data: {{$fromDate.' '.$toDate}} </h3>
                </div>
                
                
                <div class="table-responsive">
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
            @forelse ($expenses as $expense)
                <tr>
                    <td>{{$expense->t_date}}</td>
                    <td>{{$expense->account_name}}</td>
                    <td>{{$expense->ref}}</td>
                    <td>{{$expense->description}}</td>
                    <td>{{$expense->transaction_type}}</td>
                    <td>{{$expense->payment_type}}</td>
                    <td>{{$expense->amount }}</td>
                    <td>{{$expense->t_rate}}</td>
                    <td>{{$expense->t_amount}}</td>
                    <td>{{$expense->at_amount}}</td>
                     @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                    <td>
                        <a id="EditBtn" rid="{{$expense->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                        @if (auth()->user()->user_type == 11)
                        <a id="deleteBtn" rid="{{$expense->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                         @endif
                        <a href="#" rid="{{$expense->id}}" data-toggle="modal" data-target="#assignSubcat" class="trandiagnosis"><i class="fa fa-eye" style="color: #5cb85c;font-size:16px;"></i></a>
                    </td>
                    @endif
                </tr>
            @empty
                <h3>No data found. Create a new Expense</h3>
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




    </div>


@endsection
@section('scripts')
<script>
        // show hide due amount div
        // function myFunction() {
        //         var x = document.getElementById("due_amount_div");
        //         if (x.style.display === "none") {
        //             x.style.display = "block";
        //         } else {
        //             x.style.display = "none";
        //         }
        //         }
</script>
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


            //calculation start
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

            

            $("#account_id").change(function(){
                $(this).find("option:selected").each(function(){
                    var i = $(this).attr("value");

                        values=i.split('|');
                        account_id=values[0];
                        account_name=values[1];

                    if(account_name == "Tax Provision"){
                        $("#vatid").hide();
                        $("#showemployee").hide();
                        $("#transaction_type").html("<option value=''>Please Select</option><option value='Payment'>Payment</option><option value='Account Payable'>Account Payable</option>");
                    }else{
                        $("#vatid").show();
                        $("#showemployee").hide();
                        $("#transaction_type").html("<option value=''>Please Select</option><option value='Current'>Current</option><option value='Prepaid'>Prepaid</option><option value='Due'>Due</option><option value='Prepaid Adjust'>Prepaid Adjust</option>");
                    }
                });
            }).change();
            
            
   

            $("#payment_type").change(function(){
                $(this).find("option:selected").each(function(){
                    var val = $(this).val();
                    if( val == "Account Payable" ){
                        $("#showpayable").show();
                    } else{
                        $("#showpayable").hide();
                    }
                });
            }).change();


            
            $("#transaction_type").change(function(){
                $(this).find("option:selected").each(function(){
                    var val = $(this).val();
                    if( val == "Prepaid Adjust" ||  val == "Account Payable"){
                        clearPaymentTypefield();
                        $("#showpayable").hide();
                        $("#payment_div").hide();
                        $("#vatid").hide();
                    } else if(val == "Payment"){
                        $("#showpayable").hide();
                        $("#payment_div").show();
                        $("#vatid").hide();
                    }else if(val == "Due Adjust"){
                        $("#showemployee").show();
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
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Account Payable'>Account Payable</option>");
                } else if (transaction_type == "Current") {
                    $("#showpayable").hide();
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                }else if (transaction_type == "Payment") {
                    $("#showpayable").hide();
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                } else if (transaction_type == "Prepaid") {
                    $("#showpayable").hide();
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                }else if (transaction_type == "Prepaid Adjust") {
                    clearTaxPaymentTypefield();
                    $("#showpayable").hide();
                    $("#payment_type").html("<option value=''>Please Select</option>");
                }
            });





            //header for csrf-token is must in laravel
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            //

            var url = "{{URL::to('/expense')}}";
            $("#addBtn").click(function(){
                if($(this).val() == 'Create') {
                    var expense_date= $("#expense_date").val();

                    var i = $("#account_id").val();
                    values=i.split('|');
                    account_id=values[0];
                    account_name=values[1];

                    var ref= $("#ref").val();
                    var amount= $("#amount").val();
                    var tax_rate= $("#tax_rate").val();
                    var tax_amount= $("#tax_amount").val();
                    var after_tax_amount= $("#after_tax_amount").val();
                    var transaction_type= $("#transaction_type").val();
                    var payment_type= $("#payment_type").val();

                    var modelable_id= $("#modelable_id").val();
                    var employee_id= $("#employee_id").val();
                    var description= $("#description").val();
                    //console.log(expense_date+',' + account_id+',' + ref+',' + amount+',' + tax_rate +','+ tax_amount+',' + after_tax_amount+',' + payment_type+',' + transaction_type+',' + due_amount+',' + description);
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {expense_date:expense_date,account_id:account_id,account_name:account_name,ref:ref,amount:amount,tax_rate:tax_rate,tax_amount:tax_amount,after_tax_amount:after_tax_amount,transaction_type:transaction_type,payment_type:payment_type,modelable_id:modelable_id,employee_id:employee_id,description:description},
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

                    var expense_date= $("#expense_date").val();

                    var i = $("#account_id").val();
                    values=i.split('|');
                    account_id=values[0];
                    account_name=values[1];

                    var ref= $("#ref").val();
                    var amount= $("#amount").val();
                    var tax_rate= $("#tax_rate").val();
                    var tax_amount= $("#tax_amount").val();
                    var after_tax_amount= $("#after_tax_amount").val();
                    var transaction_type= $("#transaction_type").val();
                    var payment_type= $("#payment_type").val();
                    var modelable_id= $("#modelable_id").val();
                    var due_amount= $("#due_amount").val();
                    var employee_id= $("#employee_id").val();
                    var description= $("#description").val();
                    //console.log(expense_date+',' + account_id+',' + ref+',' + amount+',' + tax_rate +','+ tax_amount+',' + after_tax_amount+',' + payment_type+','+ modelable_id+',' + due_amount+',' + description);


                    $.ajax({
                        url:url+'/'+$("#expenseid").val(),
                        method: "PUT",
                        type: "PUT",
                        data:{expense_date:expense_date,account_id:account_id,account_name:account_name,ref:ref,amount:amount,tax_rate:tax_rate,tax_amount:tax_amount,after_tax_amount:after_tax_amount,transaction_type:transaction_type,payment_type:payment_type,modelable_id:modelable_id,due_amount:due_amount,employee_id:employee_id,description:description},
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
                $expenseid = $(this).attr('rid');
                $info_url = url + '/'+$expenseid+'/edit';
                $.get($info_url,{},function(d){
                    populateForm(d);
                    pagetop();

                });
            });
            //Edit Room end
            function populateForm(data){
                $("#expense_date").val(data.t_date);
                var account = data.account_id+'|'+data.account_name;
                $("#account_id").val(account);

                $("#ref").val(data.ref);

                if(data.account_name == "Tax Provision"){
                    $("#vatid").hide();
                    $("#showemployee").hide();
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Payment'>Payment</option><option value='Account Payable'>Account Payable</option>");
                }else{
                    $("#vatid").show();
                    $("#showemployee").hide();
                    $("#transaction_type").html("<option value=''>Please Select</option><option value='Current'>Current</option><option value='Prepaid'>Prepaid</option><option value='Due'>Due</option><option value='Prepaid Adjust'>Prepaid Adjust</option>");
                }

                $("#transaction_type").val(data.transaction_type);

                if( data.transaction_type == "Prepaid Adjust" ||  data.transaction_type == "Account Payable"){
                    clearPaymentTypefield();
                    $("#showpayable").hide();
                    $("#payment_div").hide();
                    $("#vatid").hide();
                } else if(data.transaction_type == "Payment"){
                    $("#showpayable").hide();
                    $("#payment_div").show();
                    $("#vatid").hide();
                }else if(data.transaction_type == "Due Adjust"){
                    $("#showemployee").show();
                    $("#payment_div").show();
                    $("#vatid").hide();
                }else{
                    $("#payment_div").show();
                    $("#vatid").show();
                    }


                if (data.transaction_type == "Due") {
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Account Payable'>Account Payable</option>");
                } else if (data.transaction_type == "Current") {
                    $("#showpayable").hide();
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                }else if (data.transaction_type == "Payment") {
                    $("#showpayable").hide();
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                } else if (data.transaction_type == "Prepaid") {
                    $("#showpayable").hide();
                    $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                }else if (data.transaction_type == "Prepaid Adjust") {
                    clearTaxPaymentTypefield();
                    $("#showpayable").hide();
                    $("#payment_type").html("<option value=''>Please Select</option>");
                }

                $("#amount").val(data.amount);
                $("#tax_rate").val(data.t_rate);
                $("#tax_amount").val(data.t_amount);
                $("#after_tax_amount").val(data.at_amount);
                $("#payment_type").val(data.payment_type);

                if( data.payment_type == "Account Payable" ){
                    $("#showpayable").show();
                } else{
                    $("#showpayable").hide();
                }


                $("#modelable_id").val(data.liability_id);
                $("#employee_id").val(data.employee_id);
                $("#description").val(data.description);
                $("#expenseid").val(data.id);
                $("#addBtn").val('Update');
                $("#addThisFormContainer").show(300);
                $("#newBtn").hide(100);
            }

            //Delete Branch
            $("#contentContainer").on('click','#deleteBtn', function(){
                if(!confirm('Sure?')) return;
                $expenseid = $(this).attr('rid');
                $info_url = url + '/'+$expenseid;
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

            function clearPaymentTypefield(){
                $('#payment_type').val('');
            }



        });

    </script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#expense").addClass('active');
    });
</script>
<script>
$.datepicker.setDefaults({
dateFormat: 'yy-mm-dd'  
});
$(function() {
    $( "#expense_date" ).datepicker();
});
</script>
@endsection

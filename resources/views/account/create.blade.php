@extends('layouts.master')
@section('content')
{{--    <h3>New Account</h3>--}}
    <div id="addThisFormContainer">

        <div class="row">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>New Account</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="ermsg">
                              
                                
                            </div>
                            <div class="container">


                            {!! Form::open(['url' => 'account/create','id'=>'createThisForm']) !!}
                            {!! Form::hidden('accountid','', ['id' => 'accountid']) !!}
                            <div>
                                <label for="account_type" class="awesome">Account Type</label>
                                <select name="account_type" class="form-control" id="account_type" required>
                                    <option value=""  >Select Account Type</option>
                                    <option value="Fixed Asset"  >Fixed Asset</option>
                                    <option value="Current Asset"  >Current Asset</option>
                                    <option value="Current Asset-AR"  >Current Asset-AR</option>
                                    <option value="Liabilities-AP"  >Liabilities-AP</option>
                                    <option value="Long-term Liabilities"  >Long-term Liabilities</option>
                                    <option value="Short-term Liabilities"  >Short-term Liabilities</option>
                                    <option value="Expense"  >Expense</option>
                                    <option value="Income"  >Income</option>
                                    <option value="Owner Equity"  >Owner Equity</option>
                                    <option value="Dividend"  >Dividend</option>
                                    <option value="Vat Receivable"  >Vat Receivable</option>
                                    <option value="Vat Payable"  >Vat Payable</option>
                                </select>
                            </div>
                            {!! Form::label('Title', 'Account Name', ['class' => 'awesome']) !!}
                            {!! Form::text('title','',['id'=>'account_name','class'=>'form-control','placeholder'=>'Account Name']) !!}

                            {!! Form::label('Description', 'Account Description', ['class' => 'awesome']) !!}
                            {!! Form::textarea('description','',['id'=>'account_desc','class'=>'form-control','rows'=>'5','placeholder'=>'Account Description','required']) !!}

                            <hr>
                            <input type="button" id="addBtn" value="Create" class="btn btn-primary">
                            <input type="button" id="FormCloseBtn" value="Close" class="btn btn-warning">
                            {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
    </div>
</div>

    </div>

    <button id="newBtn" type="button" class="btn btn-info">Add New Account</button>
    <hr>

<div id="contentContainer">


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3> Account Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="container">


                            <table class="table table-bordered table-hover" id="example">
                                <thead>
                                <tr>
                                    <th>Account Type</th>
                                    <th>Account Name</th>
                                    <th>Account Description</th>
                                    @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                    <th>Action</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($accounts as $account)
                                    <tr>
                                        <td>{{$account->account_type}}</td>
                                        <td>{{$account->account_name}}</td>
                                        <td>{{$account->account_desc}}</td>
                                        @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                            
                                            <td>
                                                <a id="EditBtn" rid="{{$account->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                                <a id="deleteBtn" rid="{{$account->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                                <a href="#" rid="{{$account->id}}" data-toggle="modal" data-target="#assignSubcat" class="accdiagnosis"><i class="fa fa-eye" style="color: #5cb85c;font-size:16px;"></i></a>
                                            </td>
                                        @endif    
                                    </tr>
                                @empty
                                    <h3>No post found from you. Create a new Account</h3>
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
            

            //header for csrf-token is must in laravel
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            //

            var url = "{{URL::to('/account')}}";
            $("#addBtn").click(function(){
                if($(this).val() == 'Create') {
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {
                            account_type: $("#account_type").val(),
                            account_name: $("#account_name").val(),
                            account_desc: $("#account_desc").val()
                        },
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
                    $.ajax({
                        url:url+'/'+$("#accountid").val(),
                        method: "PUT",
                        type: "PUT",
                        data:{
                            account_type: $("#account_type").val(),
                            account_name: $("#account_name").val(),
                            account_desc: $("#account_desc").val()
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
                $accountid = $(this).attr('rid');
                $info_url = url + '/'+$accountid+'/edit';
                $.get($info_url,{},function(d){
                    populateForm(d);
                    pagetop();
                });
            });
            //Edit Room end

      //Delete Account
            url2 =  "{{URL::to('/account_delete')}}";
            $("#contentContainer").on('click','#deleteBtn', function(){
                if(!confirm('Sure?')) return;
                $accountid = $(this).attr('rid');
                $info_url = url2 + '/'+$accountid;
                $.ajax({
                    url:$info_url,
                    method: "GET",
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
            //Delete acc
            
            function populateForm(data){
                $("#account_type").val(data.account_type);
                $("#account_name").val(data.account_name);
                $("#account_desc").val(data.account_desc);
                $("#accountid").val(data.id);
                $("#addBtn").val('Update');
                $("#addThisFormContainer").show(300);
                $("#newBtn").hide(100);
            }
            function clearform(){
                $('#createThisForm')[0].reset();
                $("#addBtn").val('Create');
            }
            
            
            
            //Modal Show
              var accurl = "{{URL::to('/account_diagnosis')}}";
             $("body").delegate(".accdiagnosis","click",function (){
                var accid = $(this).attr('rid');
                var info_url = accurl + '/'+ accid;
                console.log(info_url);
                $.ajax({
                    url:info_url,
                    method: "POST",
                    type: "POST",
                    data:{
                      
                    },
                    success: function (response) {
                    var resp = JSON.parse(JSON.stringify(response));
                // var resp = $.parseJSON(response);
				    if (resp.status == 202) {
					var reportHTML = '<table class="table table-hover table-bordered text-center"><thead><tr><th>Name</th><th>Action</th></tr></thead><tbody>';
					$.each(resp.report, function(index, value){
						reportHTML += '<tr><td>Created By :</td><td>'+ value.created_by +'</td></tr><tr><td>Created At :</td>+<td>'+ value.created_at +'</td></tr><tr><td>Created IP :</td><td>'+ value.created_ip +'</td></tr><tr><td>Updated By :</td><td>'+ value.updated_by +'</td></tr><tr><td>Updated At :</td><td>'+ value.updated_at +'</td></tr><tr><td>Updated IP :</td><td>'+ value.updated_ip +'</td></tr>';
					});
					reportHTML += '</tbody></table>';
					$("#allreport").html(reportHTML);

				}
                        },
                        error: function (d) {
                            console.log(d);
                        }
                });
            });
            //Modal Show
            
            


        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#account").addClass('active');
        });
    </script>

@endsection

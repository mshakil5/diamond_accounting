@extends('layouts.master')
@section('content')
    <div id="addThisFormContainer" xmlns:background-color="http://www.w3.org/1999/xhtml">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Room Sales</h3>
                        <div class="ermsg">


                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-3">

                                        {!! Form::open(['url' => 'regularform/form','id'=>'createThisForm']) !!}
                                        {!! Form::hidden('regularid','', ['id' => 'regularid']) !!}
                                        {!! Form::label('Title', 'Date', ['class' => 'awesome']) !!}
                                        {!! Form::date('title','',['id'=>'date','class'=>'form-control','placeholder'=>'Date']) !!}

                                    </div>

                                    <div class="col-md-3">
                                        <div>
                                            <label for="name">Name</label>
                                            <input type="text" id="name" name="name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div>
                                            <label for="agt">Agent</label>
                                            <input type="text" id="agt" name="agt" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div>
                                            <label for="ref">Ref</label>
                                            <input type="text" id="ref" name="ref" class="form-control">
                                        </div>
                                        <div>
                                            <label for="orderno">Order-No</label>
                                            <input type="text" id="orderno" name="orderno" class="form-control">
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4">

                                <div>
                                    <label for="cash">Cash</label>
                                    <input type="number" id="cash" name="cash" class="form-control" >
                                </div>
                                <div>
                                    <label for="bank">Bank</label>
                                    <input type="number" id="bank" name="bank" class="form-control" >
                                </div>
                                <div>
                                    <label for="eviivo">eviivo</label>
                                    <input type="number"  id="eviivo" name="eviivo" class="form-control"  >
                                </div>

                                <div>
                                    <label for="other_sales">Other Due</label>
                                    <input type="number"  id="other_sales" name="other_sales" class="form-control"  >
                                </div>

                                <div>
                                    <label for="advance_sales">Advance Sales</label>
                                    <input type="number"  id="advance_sales" name="advance_sales" class="form-control"  >
                                </div>
                                <div>
                                    <label for="returnamount">Return</label>
                                    <input type="number"  id="returnamount" name="returnamount" class="form-control"  >
                                </div>

                                <div>
                                    <label for="parking_cash">Parking Cash</label>
                                    <input type="number" id="parking_cash" name="parking_cash" class="form-control" >
                                </div>
                                <div>
                                    <label for="parking_card">Card</label>
                                    <input type="number" id="parking_card" name="parking_card" class="form-control" >
                                </div>

                                <div>
                                    <label for="remark">Remark</label>
                                    <input type="text"  id="remark" name="remark" class="form-control"  >
                                </div>

                                <hr class="no-print">
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

    <button id="newBtn" type="button" class="btn btn-info no-print">Add New </button>
    <hr class="no-print">
    <div id="contentContainer">


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="no-print"> Regular Form Details</h3>

                        {{-- search strt  --}}
                        <div class="no-print">
                        <form  action="{{route('regular_search')}}" method ="POST">
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
                        {{-- search end  --}}

                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="">
                                
                                <!-- this row will not appear when printing -->
                                 <div class="row no-print">
                                    <div class="col-12">
                                        <button onclick="window.print()" class="fa fa-print btn btn-default float-right">Print</button>
                                    </div>
                                  </div>
                                
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
                                    <table class="table table-bordered table-hover"   id="regularform">
                                        <thead>
                                        <tr>
                                            <td colspan="3"  style="background-color: #CCCCCC;"></td>
                                            <td colspan="7"  style="background-color: #B2B1A8; text-align: center; font-size: 20px;"><b>Room Sales</b></td>

                                            <th colspan="2" style="background-color: #FA1100;  text-align: center; font-size: 20px;">Parking</th>
                                            <th style="background-color: #CCCCCC;">Remark</th>
                                            @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                                <th style="background-color: #CCCCCC;" class="no-print">Action</th>
                                            @endif

                                        </tr>
                                        <tr>
                                            <th style="background-color: #B2A1C7;">Date</th>
                                            <th style="background-color: #D99594;">Total Reservations</th>
                                            {{-- <th style="background-color: #D99594;">OrderNumber</th> --}}
                                            <th style="background-color: #E5DFEC;">Our Ref </th>
                                            <th style="background-color: #fa9a4b;">Cash</th>
                                            <th style="background-color: #93D250;">Card/Bank <br> Transfer</th>
                                            <th style="background-color: #FCBF02;">Via System</th>
                                            <th style="background-color: #FFFFFF;">Due</th>
                                            <th style="background-color: #C0B8E4;">Advanced</th>
                                            <th style="background-color: #FFFF01;">Refund</th>
                                            <th style="background-color: #A5B6CA;">Total Room Sales</th>
                                            <th style="background-color: #92D04F;">Card</th>
                                            <th style="background-color: #fa9a4b;">Cash</th>
                                            <th style="background-color: #CCCCCC;">Remark</th>
                                            @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2 || auth()->user()->user_type == 3)
                                                <th style="background-color: #CCCCCC;" class="no-print">Action</th>
                                            @endif

                                        </tr>
                                        </thead>
                                        <tbody>

                                        @forelse ($data as $data)
                                            <tr>
                                                <td style="background-color: #B2A1C7;">{{$data->date}}</td>
                                                <td>{{$data->name}}</td>
                                                {{-- <td>{{$data->orderno}}</td> --}}
                                                <td>{{ $data->agt}}/{{$data->ref }}</td>
                                                <td>{{$data->cash}}</td>
                                                <td>{{$data->bank}}</td>
                                                <td>{{$data->eviivo}}</td>
                                                <td>{{$data->other_sales}}</td>
                                                <td>{{$data->advance_sales}}</td>
                                                <td>{{$data->returnamount}}</td>
                                                <td>{{$data->cash + $data->bank + $data->eviivo + $data->other_sales}}</td>
                                                <td>{{$data->parking_card}}</td>
                                                <td>{{$data->parking_cash}}</td>
                                                <td>{{$data->remark}}</td>
                                                
                                                @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2 || auth()->user()->user_type == 3)

                                                    <td class="no-print">
                                                        <a id="EditBtn" rid="{{$data->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                                         @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 3)
                                                        <a id="deleteBtn" rid="{{$data->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                                         @endif
                                                        <!--<a href="#" rid="{{$data->id}}" data-toggle="modal" data-target="#assignSubcat" class="trandiagnosis"><i class="fa fa-eye" style="color: #5cb85c;font-size:16px;"></i></a>-->
                                                    </td>

                                                @endif

                                            </tr>

                                        @empty
                                            <h3>No post found from you. </h3>
                                        @endforelse

                                        </tbody>

                                        <tbody>
                                        <tr>
                                            <td style="background-color: #B2A1C7;"></td>
                                            <td style="background-color: #D99594;">Balance</td>
                                            {{-- <td style="background-color: #E5DFEC;"></td> --}}
                                            <td style="background-color: #E5DFEC;"></td>
                                            <td style="background-color: #fa9a4b;">{{ $cash }}</td>
                                            <td style="background-color: #93D250;">{{ $bank }}</td>
                                            <td style="background-color: #FCBF02;">{{ $eviivo }}</td>
                                            <td style="background-color: #FFFFFF;">{{ $other_sales }}</td>
                                            <td style="background-color: #C0B8E4;">{{ $advance_sales }}</td>
                                            <td style="background-color: #FFFF01;">{{ $returnamount }}</td>
                                            <td style="background-color: #A5B6CA;">{{ $cash + $bank + $eviivo + $other_sales  }}</td>
                                            <td style="background-color: #92D04F;">{{ $parking_card }}</td>
                                            <td style="background-color: #fa9a4b;">{{ $parking_cash }}</td>
                                            <td style="background-color: #CCCCCC;"></td>
                                            <td style="background-color: #CCCCCC;"></td>
                                        </tr>

                                        <tr>
                                            <td></td>
                                            <td></td>

                                            <td  style="background-color: #CCCCCC;" colspan="9" style="font-size: 18px"><strong>Net Sale: Total Sale[Cash+Bank+eviivo+other due]-[Advance+Return] = {{ $cash + $bank + $eviivo + $other_sales - $returnamount -$advance_sales }}</strong></td>

                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>


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

            var url = "{{URL::to('/regular')}}";
            $("#addBtn").click(function(){
                if($(this).val() == 'Create') {
                    var name= $("#name").val();
                    var agt= $("#agt").val();
                    var ref= $("#ref").val();
                    var orderno= $("#orderno").val();
                    var cash= $("#cash").val();
                    var bank= $("#bank").val();
                    var eviivo= $("#eviivo").val();
                    var parking_cash= $("#parking_cash").val();
                    var parking_card= $("#parking_card").val();
                    var other_sales= $("#other_sales").val();
                    var returnamount= $("#returnamount").val();
                    var advance_sales= $("#advance_sales").val();
                    var remark= $("#remark").val();
                    var date= $("#date").val();

                    //console.log(name +','+ agt +','+ ref +','+ cash +','+ bank +','+ eviivo+',' + parking_cash+',' + parking_card+',' + other_sales+',' + returnamount+','  + advance_sales+',' + remark+',' + date);

                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {name:name,agt:agt,ref:ref,orderno:orderno,cash:cash,bank:bank,eviivo:eviivo,parking_cash:parking_cash,parking_card:parking_card,other_sales:other_sales,returnamount:returnamount,advance_sales:advance_sales,remark:remark,date:date},
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
                //Update reguar
                if($(this).val() == 'Update'){
                    $.ajax({
                        url:url+'/'+$("#regularid").val(),
                        method: "PUT",
                        type: "PUT",
                        data:{
                            name: $("#name").val(),
                            agt: $("#agt").val(),
                            ref: $("#ref").val(),
                            orderno: $("#orderno").val(),
                            cash: $("#cash").val(),
                            bank: $("#bank").val(),
                            eviivo: $("#eviivo").val(),
                            parking_cash: $("#parking_cash").val(),
                            parking_card: $("#parking_card").val(),
                            other_sales: $("#other_sales").val(),
                            returnamount: $("#returnamount").val(),
                            advance_sales: $("#advance_sales").val(),
                            remark: $("#remark").val(),
                            date: $("#date").val()
                        },
                        success: function(d){
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
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
                $regularid = $(this).attr('rid');
                $info_url = url + '/'+$regularid+'/edit';
                console.log($info_url);
                $.get($info_url,{},function(d){
                    pagetop();
                    populateForm(d);
                });
            });
            //Edit Room end

            //Delete Branch
            $("#contentContainer").on('click','#deleteBtn', function(){
                if(!confirm('Sure?')) return;
                $regularid = $(this).attr('rid');
                $info_url = url + '/'+$regularid;
                $.ajax({
                    url:$info_url,
                    method: "DELETE",
                    type: "DELETE",
                    data:{
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
            });
            //Delete Branch




            function populateForm(data){
                $("#name").val(data.name);
                $("#agt").val(data.agt);
                $("#ref").val(data.ref);
                $("#orderno").val(data.orderno);
                $("#cash").val(data.cash);
                $("#bank").val(data.bank);
                $("#eviivo").val(data.eviivo);
                $("#parking_cash").val(data.parking_cash);
                $("#parking_card").val(data.parking_card);
                $("#other_sales").val(data.other_sales);
                $("#returnamount").val(data.returnamount);
                $("#advance_sales").val(data.advance_sales);
                $("#remark").val(data.remark);
                $("#date").val(data.date);
                $("#regularid").val(data.id);
                $("#addBtn").val('Update');
                $("#addThisFormContainer").show(300);
                $("#newBtn").hide(100);
            }
            function clearform(){
                $('#createThisForm')[0].reset();
                $("#addBtn").val('Create');
            }


        });


    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#regular").addClass('active');
        });
        
    </script>
@endsection

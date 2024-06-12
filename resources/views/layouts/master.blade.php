<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="description" content="Vali is a responsive and free admin theme built with Bootstrap 4, SASS and PUG.js. It's fully customizable and modular.">
    <!-- Twitter meta-->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:site" content="@pratikborsadiya">
    <meta property="twitter:creator" content="@pratikborsadiya">
    <!-- Open Graph Meta-->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Vali Admin">
    <meta property="og:title" content="Vali - Free Bootstrap 4 admin theme">
    <meta property="og:description" content="Vali is a responsive and free admin theme built with Bootstrap 4, SASS and PUG.js. It's fully customizable and modular.">
    <title>
        @yield('title')
    </title>
    <meta charset="utf-8">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/main.css') }}">
    {{-- date picker  --}}
    <link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/base/jquery-ui.css" rel="stylesheet" />

       <!-- Font Awesome CSS-->
       <link rel="stylesheet" href="{{URL::to('assets/vendor/font-awesome/css/font-awesome.min.css')}}">



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g==" crossorigin="anonymous" />

    <!-- for export -->
    <link href="{{URL::to('assets1/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <link href="{{URL::to('assets1/css/style.css')}}" rel="stylesheet">
 
</head>
<body class="app sidebar-mini rtl">
<!-- Navbar-->
<header class="app-header">
    <a class="app-header__logo" href="#">
        
     @if(Auth::user()->branch_id == '1')   
    <img class="" src="{{ asset('img/1.jpg') }}" height="50px" width="200px" style="margin-left: -12px;" alt="Logo Image">
    @endif
    
    @if(Auth::user()->branch_id == '3')   
    <img class="" src="{{ asset('img/3.jpg') }}" height="50px" width="200px" style="margin-left: -12px;" alt="Logo Image">
    @endif
    
    @if(Auth::user()->branch_id == '4')   
    <img class="" src="{{ asset('img/4.jpg') }}" height="50px" width="200px" style="margin-left: -12px;" alt="Logo Image">
    @endif
    
    @if(Auth::user()->branch_id == '5')   
    <img class="" src="{{ asset('img/5.jpg') }}" height="50px" width="200px" style="margin-left: -12px;" alt="Logo Image">
    @endif

    @if(Auth::user()->branch_id == '6')   
    <img class="" src="{{ asset('img/6.png') }}" height="50px" width="200px" style="margin-left: -12px;" alt="Logo Image">
    @endif
    
    </a>
    <!-- Sidebar toggle button--><a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
    <!-- Navbar Right Menu-->
    <ul class="app-nav">
        
        <!--Notification Menu-->
        <!-- User Menu-->

        <li class="dropdown"><a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu"><i class="fa fa-user fa-lg"></i></a>
            <ul class="dropdown-menu settings-menu dropdown-menu-right">
                <li><a class="dropdown-item" href="{{ route('logout') }}"  onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out fa-lg"></i>{{ __('Logout') }}</a><form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form></li>

            </ul>
        </li>
    </ul>
</header>



<!-- Sidebar menu-->

@include('inc/sidebar')


<main class="app-content">


@yield('content')

<!--// model for all diagonosis report -->
<div class="container">
   <!-- Modal -->
  <div class="modal fade in" id="assignSubcat" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="get_subcat">
            
        <h4>Report</h4>
		<button type="button" class="close" data-dismiss="modal">×</button>
		
		</div>    
			    
		<div class="modal-body modal-scrol">
		    
		    <div id="allreport">
		        
		    </div>
		
			
	    </div>
				
	<div class="modal-footer">
	  
	  <button type="button" style="float:left;" class="btn btn-default" data-dismiss="modal"> Close </button>
	  
        </div>

        
	      </div> 
	      
        </div>
      </div>
	</div>
<!--// end model for all dia gonosis report 	-->

</main>
<!-- Essential javascripts for application to work-->
<script src="{{ asset('assets/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{URL::to('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{URL::to('assets/js/grasp_mobile_progress_circle-1.0.0.min.js')}}"></script>
<script src="{{URL::to('assets/vendor/jquery.cookie/jquery.cookie.js')}}"> </script>
<script src="{{URL::to('assets/vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{URL::to('assets/vendor/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{URL::to('assets/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js')}}"></script>


<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
<!-- The javascript plugin to display page loading on top-->
<script src="{{ asset('assets/js/plugins/pace.min.js') }}"></script>


<!-- Page specific javascripts-->

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/jquery-ui.min.js"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!-- for export all -->
<script src="{{URL::to('assets1/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{URL::to('assets1/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

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

@yield('scripts')
<script>
    $.datepicker.setDefaults({
    dateFormat: 'yy-mm-dd'  
    });
    $(function() {
        $( "#fromDate" ).datepicker();
        $( "#toDate" ).datepicker();
    });
    
// page schroll top
    function pagetop() {
        window.scrollTo({
        top: 130,
        behavior: 'smooth',
        });
        }
            
</script>
    <!-- export Scripts -->
<script>
$(document).ready(function(){
    
    var branch_name = 'Branch: '+{!! json_encode(auth()->user()->branch->branch_name) !!};
    var branch_phone = 'Phone: '+{!! json_encode(auth()->user()->branch->branch_phone) !!};
    var branch_address = 'Address: '+{!! json_encode(auth()->user()->branch->branch_address) !!};
    var title = 'Report: '+{!! json_encode($title) !!};
    var data = 'Data: '+{!! json_encode($fromDate) !!}+' '+{!! json_encode($toDate) !!};
    
    $('#example').DataTable({
        pageLength: 25,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        columnDefs: [ { type: 'date', 'targets': [0] } ],
        order: [[ 0, 'desc' ]],
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {extend: 'copy'},
            {extend: 'excel', title: title},
            {extend: 'pdfHtml5',
            title: 'Report',
            orientation : 'landscape',
                header:true,
                customize: function ( doc ) {
                    doc.content.splice(0, 1, {
                            text: [
                                       { text: branch_name+'\n',bold:true,fontSize:15 },
                                       { text: branch_phone+'\n',italics:true,fontSize:12 },
                                       { text: branch_address+'\n'+'\n',italics:true,fontSize:12 },
                                       { text: data+'\n',bold:true,fontSize:12 },
                                       { text: title+'\n',bold:true,fontSize:15 }

                            ],
                            margin: [0, 0, 0, 12],
                            alignment: 'center'
                        });
                    doc.defaultStyle.alignment = 'center'
                } 
            },
            {extend: 'print',
            title: "<p style='text-align:center;'>"+branch_name+"<br>"+branch_phone+"<br>"+branch_address+"<br>"+data+"<br>"+title+"</p>",
            header:true,
                customize: function (win){
                $(win.document.body).addClass('white-bg');
                $(win.document.body).css('font-size', '10px');
                $(win.document.body).find('table')
                .addClass('compact')
                .css('font-size', 'inherit');
            }
            }
        ]
    });
    
//   for rgular form table 
    $('#regularform').DataTable({
        pageLength: 25,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        "ordering": false,
        columnDefs: [ { type: 'date', 'targets': [0] } ],
        order: [[ 0, 'desc' ]],
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {extend: 'copy'},
            {extend: 'excel', title: title},
            // {extend: 'pdfHtml5',
            // title: 'Report',
            // orientation : 'landscape',
            //     header:true,
            //     customize: function ( doc ) {
            //         doc.content.splice(0, 1, {
            //                 text: [
            //                           { text: branch_name+'\n',bold:true,fontSize:15 },
            //                           { text: branch_phone+'\n',italics:true,fontSize:12 },
            //                           { text: branch_address+'\n'+'\n',italics:true,fontSize:12 },
            //                           { text: data+'\n',bold:true,fontSize:12 },
            //                           { text: title+'\n',bold:true,fontSize:15 }

            //                 ],
            //                 margin: [0, 0, 0, 12],
            //                 alignment: 'center'
            //             });
            //         doc.defaultStyle.alignment = 'center'
            //     } 
            // }
        ]
    });
// data table for regular form end 


});

// get diagonosis 
            //Modal Show
              var tran = "{{URL::to('/tran_diagnosis')}}";
             $("body").delegate(".trandiagnosis","click",function (){
                var tranid = $(this).attr('rid');
                var info_url = tran + '/'+ tranid;
                console.log(info_url);
                $.ajax({
                    url:info_url,
                    method: "POST",
                    type: "POST",
                    data:{
                      
                    },
                    success: function (response) {
                        console.log(response);
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


</script>
<script type="text/javascript">
        $(document).ready(function() {
            $("#regularform_length").addClass('no-print');
            $("#regularform_paginate").addClass('no-print');
            $("#regularform_info").addClass('no-print');
            $("#regularform_filter").addClass('no-print');
            $(".html5buttons").addClass('no-print');
            $("#ui-datepicker-div").addClass('no-print');
        });
        
</script>
 <!-- Main File-->
 <script src="{{URL::to('assets/js/front.js')}}"></script>
</body>
</html>

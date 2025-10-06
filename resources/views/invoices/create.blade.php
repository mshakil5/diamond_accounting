@extends('layouts.master')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <div id="addThisFormContainer">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Invoice</h3>
                        <div class="ermsg"></div>
                    </div>
                    <div class="card-body">
                        <div class="">
                            <div class="">

                                <form id="createThisForm">
                                    @csrf
                                    <input type="hidden" class="form-control" id="codeid" name="codeid">
                                    
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Invoice Number</label>
                                                <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="{{$invoiceNumber}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Bill For <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="invoice_for" name="invoice_for" required value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Date <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" id="invoice_date" name="invoice_date" required value="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>
                                                    Bill To <span class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control summernote"  id="bill_to" name="bill_to"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">Invoice Items</h3>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-bordered" id="invoiceItemsTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Description</th>
                                                                <th width="20%">Period</th>
                                                                <th width="15%">Price</th>
                                                                <th width="5%">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <textarea class="form-control description summernote" name="description[]" required></textarea>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control period" name="period[]" required>
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control unit_price" name="price[]" min="0" step="0.01" required>
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-success" id="addCustomItemBtn">
                                                                        <i class="fa fa-plus"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Bank Information</label>
                                                <textarea class="form-control summernote" id="bank_information" name="bank_information" rows="5">
                                                </textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group row">
                                                        <label class="col-sm-6 col-form-label">Subtotal:</label>
                                                        <div class="col-sm-6">
                                                            <input type="text" class="form-control" id="subtotal" name="subtotal" readonly value="0.00">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-6 col-form-label">VAT %:</label>
                                                        <div class="col-sm-6">
                                                            <input type="number" class="form-control" id="vat_percent" name="vat_percent" min="0" max="100" value="0">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-6 col-form-label">Total VAT:</label>
                                                        <div class="col-sm-6">
                                                            <input type="text" class="form-control" id="vat_amount" name="vat_amount" readonly value="0.00">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-6 col-form-label">Discount %:</label>
                                                        <div class="col-sm-6">
                                                            <input type="number" class="form-control" id="discount_percent" name="discount_percent" min="0" max="100" value="0">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-6 col-form-label">Discount Amount:</label>
                                                        <div class="col-sm-6">
                                                            <input type="text" class="form-control" id="discount_amount" name="discount_amount" readonly value="0.00">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-6 col-form-label"><strong>Net Total:</strong></label>
                                                        <div class="col-sm-6">
                                                            <input type="text" class="form-control" id="net_amount" name="net_amount" readonly value="0.00">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 d-none">
                                            <div class="form-group">
                                                <label>Email Body</label>
                                                <textarea class="form-control summernote" name="email_body">

                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                </form>



                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Save</button>
                        <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <button id="newBtn" type="button" class="btn btn-info">Add New</button>
    <hr>
    <div id="contentContainer">



        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>All invoices</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="container">


                            <table class="table table-bordered table-hover" id="example1">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Inv. No.</th>
                                    <th>Invoice to</th>
                                    <th>Invoice For</th>
                                    <th>Amount</th>
                                    @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 2)
                                    <th>Action</th>
                                    @endif

                                </tr>
                                </thead>
                                <tbody>


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
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>

<script>
$(document).ready(function () {

    function initSummernote(selector = '.summernote') {
        $(selector).summernote({
            height: 80,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']]
            ]
        });
    }
    initSummernote();

    $("#addThisFormContainer").hide();
    $("#newBtn").on("click", function() {
        clearForm();
        $("#newBtn").hide();
        $("#addThisFormContainer").slideDown();
    });

    $("#FormCloseBtn").on("click", function() {
        $("#addThisFormContainer").slideUp();
        $("#newBtn").show();
        clearForm();
    });

    // Laravel CSRF setup
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // URLs
    const url = "{{ URL::to('/invoices') }}";
    const upurl = "{{ URL::to('/invoices-update') }}";

    // Add or Update Invoice
    $("#addBtn").on("click", function() {
        const isCreate = $(this).val() === 'Create';
        const endpoint = isCreate ? url : upurl;

        const formData = new FormData($('#createThisForm')[0]);
        $(this).prop('disabled', true);

        $.ajax({
            url: endpoint,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                $(".ermsg").html(res.message);
                clearForm();
                if (res.redirect) window.open(res.redirect, '_blank');
                reloadTable();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            },
            complete: function() {
                $("#addBtn").prop('disabled', false);
            }
        });
    });

    // Clear form
    function clearForm() {
        $('#createThisForm')[0].reset();
        $("#addBtn").val('Create');
        $("#subtotal, #vat_amount, #discount_amount, #net_amount").val('0.00');
        $('.summernote').summernote('code', '');
    }

    $('#addCustomItemBtn').click(function() { 
        addRowToInvoiceTable();
    });

    function addRowToInvoiceTable() {
        const html = `
            <tr>
                <td><textarea class="form-control description summernote" name="description[]" required></textarea></td>
                <td><input type="text" class="form-control period" name="period[]" required></td>
                <td><input type="number" class="form-control unit_price" name="price[]" min="0" step="0.01" required></td>
                <td><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fa fa-trash-o"></i></button></td>
            </tr>
        `;
        $('#invoiceItemsTable tbody').append(html);
        initSummernote('.summernote'); // reinit summernote
    }

    $(document).on('click', '.removeRow', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    $(document).on('input', '.unit_price, #vat_percent, #discount_percent', function() {
        calculateTotals();
    });

    function calculateTotals() {
        let subtotal = 0;

        $('#invoiceItemsTable tbody tr').each(function() {
            const price = parseFloat($(this).find('.unit_price').val()) || 0;
            subtotal += price;
        });

        const vatPercent = parseFloat($('#vat_percent').val()) || 0;
        const vatAmount = (subtotal * vatPercent) / 100;

        const discountPercent = parseFloat($('#discount_percent').val()) || 0;
        const discountAmount = (subtotal * discountPercent) / 100;

        const netTotal = subtotal + vatAmount - discountAmount;

        $('#subtotal').val(subtotal.toFixed(2));
        $('#vat_amount').val(vatAmount.toFixed(2));
        $('#discount_amount').val(discountAmount.toFixed(2));
        $('#net_amount').val(netTotal.toFixed(2));
    }


    let ajaxUrl = "{{ route('admin.invoices') }}";

    var table = $('#example1').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: ajaxUrl + window.location.search,
            type: "GET",
            data: function (d) {
                // d.status_filter = $('#statusFilter').val();
                // d.client_filter = $('#clientFilter').val();
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        },
        columns: [
            // {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'date', name: 'date', orderable: false},
            {data: 'invoice_number', name: 'invoice_number', orderable: false},
            {data: 'bill_to', name: 'bill_to'},
            {data: 'invoice_for', name: 'invoice_for'},
            {data: 'net_amount', name: 'net_amount', render: function(data) {
                return '£' + parseFloat(data).toFixed(0);
            }},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        responsive: true,
        lengthChange: false,
        autoWidth: false,
    });

    function reloadTable() {
        table.ajax.reload(null, false);
    }




});
</script>
@endsection


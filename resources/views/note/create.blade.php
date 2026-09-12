@extends('layouts.master')

@section('content')
    <!-- Include Summernote CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css" rel="stylesheet">

    <div id="addThisFormContainer">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>New Note</h3>
                        <div class="ermsg"></div>
                    </div>
                    <div class="card-body">
                        <div class="container">
                            {!! Form::open(['url' => 'note/create','id'=>'createThisForm']) !!}
                            {!! Form::hidden('noteid','', ['id' => 'noteid']) !!}

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="date" class="font-weight-bold">Date</label>
                                    <input type="date" id="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="status" class="font-weight-bold">Status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="note" class="font-weight-bold">Note Content</label>
                                <textarea id="note" name="note" class="form-control" required></textarea>
                            </div>

                            <hr>
                            <input type="button" id="addBtn" value="Create" class="btn btn-primary px-4">
                            <input type="button" id="FormCloseBtn" value="Close" class="btn btn-secondary">
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button id="newBtn" type="button" class="btn btn-info">Add New Note</button>
    <hr>

    <div id="contentContainer">
        <div class="card">
            <div class="card-header">
                <h3>Note Details</h3>
            </div>

            <!-- === SUMMARY SECTION START === -->
            <div class="card-body pt-0">
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="card text-white bg-dark">
                            <div class="card-body p-3">
                                <div class="text-uppercase small font-weight-bold">Total Notes</div>
                                <div class="h5 mt-1 mb-0">{{ $summary['total'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body p-3">
                                <div class="text-uppercase small font-weight-bold">Active</div>
                                <div class="h5 mt-1 mb-0">{{ $summary['active'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body p-3">
                                <div class="text-uppercase small font-weight-bold">Inactive</div>
                                <div class="h5 mt-1 mb-0">{{ $summary['inactive'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- === SUMMARY SECTION END === -->

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Date</th>
                                <th>Note Preview</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $n = 1; ?>
                            @forelse ($notes as $nt)
                                <tr>
                                    <td>{{ $n++ }}</td>
                                    <td>{{ date('d M Y', strtotime($nt->date)) }}</td>
                                    <td style="max-width: 400px;">
                                        {{-- Stripping tags for the table preview to keep layout clean --}}
                                        {{ Illuminate\Support\Str::limit(strip_tags($nt->note), 100) }}
                                    </td>
                                    <td>
                                        @if($nt->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-warning">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a id="EditBtn" rid="{{ $nt->id }}" style="cursor:pointer;">
                                            <i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i>
                                        </a>
                                        <a id="deleteBtn" rid="{{ $nt->id }}" style="cursor:pointer;">
                                            <i class="fa fa-trash-o" style="color: red;font-size:16px;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center"><h3>No notes found. Create a new one.</h3></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $notes->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Include Summernote JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.js"></script>
    
    <script>
        $(document).ready(function () {
            // Initialize Summernote
            $('#note').summernote({
                placeholder: 'Write your note here...',
                tabsize: 2,
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear', 'italic']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            $("#addThisFormContainer").hide();

            $("#newBtn").click(function () {
                clearform();
                $("#newBtn").hide(100);
                $("#addThisFormContainer").show(300);
            });

            $("#FormCloseBtn").click(function () {
                $("#addThisFormContainer").hide(200);
                $("#newBtn").show(100);
                clearform();
            });

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            var url = "{{ URL::to('/note') }}";

            $("#addBtn").click(function () {
                // Get HTML content from Summernote
                var noteContent = $('#note').summernote('code');

                var data = {
                    date: $("#date").val(),
                    note: noteContent,
                    status: $("#status").val()
                };

                if ($(this).val() == 'Create') {
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: data,
                        success: function (d) { handleResponse(d); },
                        error: function (d) { console.log(d); }
                    });
                } else if ($(this).val() == 'Update') {
                    $.ajax({
                        url: url + '/' + $("#noteid").val(),
                        method: "PUT",
                        data: data,
                        success: function (d) { handleResponse(d); },
                        error: function (d) { console.log(d); }
                    });
                }
            });

            $("#contentContainer").on('click', '#EditBtn', function () {
                $noteid = $(this).attr('rid');
                $info_url = url + '/' + $noteid + '/edit';
                $.get($info_url, {}, function (d) {
                    populateForm(d);
                    if (typeof pagetop === 'function') pagetop();
                });
            });

            $("#contentContainer").on('click', '#deleteBtn', function () {
                if (!confirm('Are you sure you want to delete this note?')) return;
                $noteid = $(this).attr('rid');
                $.ajax({
                    url: url + '/' + $noteid,
                    method: "DELETE",
                    success: function (d) {
                        if (d.success) {
                            alert(d.message);
                            location.reload();
                        } else {
                            alert(d.message);
                        }
                    },
                    error: function (d) { console.log(d); }
                });
            });

            function handleResponse(d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                    if (typeof pagetop === 'function') pagetop();
                } else if (d.status == 300) {
                    $(".ermsg").html(d.message);
                    if (typeof pagetop === 'function') pagetop();
                    window.setTimeout(function () { location.reload() }, 2000);
                }
            }

            function populateForm(data) {
                $("#date").val(data.date);
                $("#status").val(data.status);
                
                // Populate Summernote with HTML content
                $('#note').summernote('code', data.note);
                
                $("#noteid").val(data.id);
                $("#addBtn").val('Update');
                $("#addThisFormContainer").show(300);
                $("#newBtn").hide(100);
            }

            function clearform() {
                $('#createThisForm')[0].reset();
                $("#addBtn").val('Create');
                $("#noteid").val('');
                $(".ermsg").html('');
                
                // Clear Summernote content
                $('#note').summernote('reset');
            }
        });
    </script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            $("#financial_statement").addClass('active');
            $("#financial_statement").addClass('is-expanded');
            $("#note_active").addClass('active');
        });
    </script>
@endsection
@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> User Registration</h4>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('admin.register.submit') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="branch_id">Branch</label>

                                    <select name="branch_id" class="form-control">
                                        <option value="" disabled selected>Please Select Branch</option>
                                        @foreach($branch as $branches)
                                            <option value="{{$branches->id}}">{{ $branches->branch_name }}</option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror                      
                                </div>

                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text"name="phone"  class="form-control" value="{{old('phone')}}" placeholder="Enter Phone" id="phone">
                                </div>
                                <div class="form-group">
                                    <label for="user_type">User Role</label>
                                    <select name="user_type" class="form-control">
                                        <option value="11">Super Admin</option>
                                        <option value="1">Read Full</option>
                                        <option value="2">Dada Entry</option>
                                        <option value="3">Sales Entry</option>

                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email </label>
                               
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value=" " required autocomplete="email">
    
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                   

                                </div>

                                
                                <div class="form-group">
                                    <label for="address">Address </label>
                                    <input type="text" name="address" class="form-control" value="{{old('address')}}" placeholder="Enter Address" id="address">
                                </div>

                                <div class="form-group">
                                    <label for="pwd">Password:</label>
                                    {{-- <input type="password" name="password" class="form-control" value="" placeholder="Enter password" id="pwd"> --}}
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror

                                </div>

                                <div class="form-group">
                                    <label for="cpwd">Confirm Password:</label>
                                    {{-- <input type="password" class="form-control" name="password_confirmation" placeholder="Enter password" id="cpwd"> --}}
                                    {{-- <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label> --}}

                                    {{-- <div class="col-md-6"> --}}
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                    {{-- </div> --}}
                                </div>


                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                                <a href="/manage-admin" type="submit" class="btn btn-danger">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#alluser").addClass('active');
            $("#alluser").addClass('is-expanded');
            $("#custom-register").addClass('active');
        });
    </script>
@endsection

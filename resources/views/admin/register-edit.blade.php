@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{url('/role-register-update/'.$users->id) }}" method="POST">
                                {{ csrf_field() }}
                                {{ method_field('PUT') }}
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
                                    <label for="name">Name</label>
                                    <input type="text" name="name" value="{{$users->name}}" class="form-control" placeholder="Enter Name" id="name">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text"name="phone" value="{{$users->phone}}" class="form-control" placeholder="Enter Phone" id="phone">
                                </div>
                                <div class="form-group">
                                    <label for="user_type">User Role</label>
                                    <select name="user_type" class="form-control">
                                        <option value="1">Read Full</option>
                                        <option value="2">Dada Entry</option>
                                        <option value="3">Sales Entry</option>

                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email </label>
                                    <input type="email" name="email" value="{{$users->email}}" class="form-control" placeholder="Enter Email" id="email">
                                </div>
                                <div class="form-group">
                                    <label for="address">Address </label>
                                    <input type="text" name="address" value="{{$users->address}}" class="form-control" placeholder="Enter Address" id="address">
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{url('/manage-admin') }}" type="submit" class="btn btn-danger">Cancel</a>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
@endsection

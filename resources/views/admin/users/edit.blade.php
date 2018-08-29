@extends('cms::layouts.dashboard')

@section('pageTitle') Users: Edit @stop

@section('content')

    <div class="col-md-12 mt-4">
        @if (! Session::get('original_user'))
            <a class="btn btn-outline-primary float-right" href="{{ url('/admin/users/switch/'. $user->id) }}">Login as this User</a>
        @endif
    </div>

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link @if (Request::is(config('cms.backend-route-prefix', 'cms').'/users/*/edit')) {{ 'active' }} @endif" href="{{url("admin/users/{$user->id}/edit") }}">Profile</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Referrals</a>
        </li>
    </ul>

    <div class="col-md-12 mt-4">
        {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'patch']) !!}

        {!! FormMaker::fromObject($user,
           [
           'name',
           'meta[first_name]' => [
                'alt_name' => 'First Name',
            ],
           'meta[last_name]' => [
                'alt_name' => 'Last Name',
            ],
           'email' => [
                'custom' => 'readonly'
           ],
           'company_id' => [
                'type' => 'select',
                'alt_name' => 'Company',
                'options' => $companies
            ],

            'active' => [
                 'type' => 'checkbox',
                 'alt_name' => 'Active'
            ]
           ] ) !!}


        <div class="form-group">
            <label class="control-label" for="Meta[roles]">Roles</label>
            <select class="js-example-basic-multiple select2 form-control" name="roles[]" multiple="multiple">

                @foreach($roles as $role)
                    <option @if($user->hasRole($role->name)) {{ 'selected'}} @endif value="{{ $role->name }}">{{ $role->label }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-4">
            <a class="btn btn-secondary float-left" href="{{ URL::previous() }}">Cancel</a>
            <button class="btn btn-primary float-right" type="submit">Save</button>
        </div>

        {!! Form::close() !!}


        {{--<div class="mt-4">
            <a class="btn btn-secondary float-left" href="{{ URL::previous() }}">Cancel</a>
            <button class="btn btn-primary float-right" type="submit">Save</button>
        </div>--}}
    </div>

@stop

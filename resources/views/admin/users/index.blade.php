@extends('cms::layouts.dashboard')

@section('pageTitle') Users @stop

@section('content')

    <div class="col-md-12">
        <nav class="navbar px-0 navbar-light justify-content-between">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="btn btn-primary" href="{{ url('admin/users/invite') }}">Invite New User</a>
                </li>
            </ul>
        </nav>
    </div>


    <br/>
    <div class="well">
        {!! Form::open(['url' => 'admin/users/search', 'method' => 'GET', 'class' => 'mt-2',  'role'=>'search']) !!}


        <div class="row">
            <div class="col col-lg-1">
                {{ Form::label('id', 'ID') }}
                {{ Form::number('id',  \Input::get('id'), array('class' => 'form-control', 'placeholder' => 'ID')) }}
            </div>
            <div class="col col-lg-2">
                {{ Form::label('name', 'Name') }}
                {{ Form::text('name',  \Input::get('name'), array('class' => 'form-control', 'placeholder' => 'Name')) }}

            </div>

            <div class="col col-lg-2">
                {{ Form::label('email', 'Email') }}
                {{ Form::text('email',  \Input::get('email'), array('class' => 'form-control', 'placeholder' => 'Email')) }}
            </div>


            <div class="col col-lg-2">
                <div class="form-group">
                    <label for="company">Company</label>
                    <select name="company" class="form-control" id="company">
                        <option disabled selected value> -- select an option -- </option>
                        @foreach($companies as $name => $id)
                            <option {{\Input::post('company') == $id ? 'selected' : '' }} value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col col-lg-2">
                {{ Form::label('', '') }}
              {{--  {{ Form::label('active', 'Active') }}
                {{ Form::checkbox('active', 'value') }}
--}}
                <div class="form-check">
                    {{ Form::checkbox('active', 1, true) }}
                    <label class="form-check-label" for="active">
                        Active
                    </label>
                </div>
            </div>

            <div class="col col-lg-2">

                <br />
            {{ Form::submit('Search', array('class' => 'btn btn-outline-success my-2 my-sm-0')) }}

            </div>

        </div>

        {{ Form::close() }}

    </div>

    <br/>
    <br/>



    <div class="col-md-12">
        @if ($users->count() === 0)
            <div class="card card-dark text-center mt-4">
                @if (request('search'))
                    <div class="card-header">Searched for "{{ request('search') }}"</div>
                @endif
                <div class="card-body">No users found.</div>
            </div>
        @else
            <table class="table table-striped">
                <thead>
                <th>{!! sortable('ID', 'id') !!}</th>
                <th>{!! sortable('Name', 'name') !!}</th>
                <th>{!! sortable('Email', 'email') !!}
                <th>{!! sortable('Company', 'company_id') !!}</th>
                <th>Roles</th>
                <th>{!! sortable('Active', 'is_active') !!}</th>
                <th>{!! sortable('Created', 'created_at') !!}</th>
                <th width="170px" class="text-right">Actions</th>
                </thead>
                <tbody>
                @foreach($users as $user)
                        <tr class="{{ $user->id == Auth::id() ? 'table-active' : ''}}">
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td><a href="/admin/users/{{$user->id}}/edit">{{ $user->email }}</a></td>
                            <td>{{ !empty($user->company->name) ? $user->company->name : null }}</td>
                            <td>@foreach($user->roles as $role){{ $loop->first ? '' : ', ' }}{{ $role->label }} @endforeach</td>
                            <td class="raw-m-hide">@if ($user->active) <span class="fa fa-check"></span> @else <span
                                        class="fa fa-close"></span> @endif </td>
                            <td>{{ $user->created_at }}</td>
                            <td class="text-right">
                                <div class="btn-toolbar justify-content-between">
                                    <a class="btn btn-outline-primary btn-sm mr-2"
                                       href="{{ url('admin/users/'.$user->id.'/edit') }}"><span
                                                class="fa fa-edit"></span> Edit</a>
                                    <form method="post" action="{!! url('admin/users/'.$user->id) !!}">
                                        {!! csrf_field() !!}
                                        {!! method_field('DELETE') !!}
                                        <button class="btn btn-danger btn-sm" type="submit"
                                                onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

@stop

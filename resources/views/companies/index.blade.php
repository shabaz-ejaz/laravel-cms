@extends('cms::layouts.dashboard', ['pageTitle' => 'Companies &raquo; Index'])


@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right raw-margin-top-24 raw-margin-left-24">
                {!! Form::open(['route' => 'companies.search']) !!}
                <input class="form-control form-inline pull-right" name="search" placeholder="Search">
                {!! Form::close() !!}
            </div>
            <h1 class="pull-left">Companies</h1>
            <a class="btn btn-primary pull-right raw-margin-top-24 raw-margin-right-8" href="{!! route('companies.create') !!}">Add New</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if ($companies->isEmpty())
                <div class="well text-center">No companies found.</div>
            @else
                <table class="table table-striped">
                    <thead>
                    <th>{!! sortable('Name', 'name') !!}</th>
                    <th>Industry</th>
                    <th>Number of staff</th>
                    <th>Actual number of staff</th>
                    <th>{!! sortable('Active', 'active') !!}</th>
                    <th>{!! sortable('Created', 'created_at') !!}</th>
                        <th class="text-right" width="200px">Action</th>
                    </thead>
                    <tbody>
                        @foreach($companies as $company)
                            <tr>
                                <td>
                                    <a href="{!! route('companies.edit', [$company->id]) !!}">{{ $company->name }}</a>
                                </td>
                                <td>
                                    @if(isset($company->industry)) {{ $industries[$company->industry] }} @endif
                                </td>

                                </td>
                                <td>
                                    {{ $company->number_of_staff }}
                                </td>

                                <td>
                                    {{ $company->actual_number_of_staff }}
                                </td>
                                <td class="raw-m-hide">@if ($company->active) <span class="fa fa-check"></span> @else <span class="fa fa-close"></span> @endif </td>

                                <td>
                                    {{ $company->created_at }}
                                </td>
                                <td class="text-right">
                                    <form method="post" action="{!! route('companies.destroy', [$company->id]) !!}">
                                        {!! csrf_field() !!}
                                        {!! method_field('DELETE') !!}
                                        <button class="btn btn-danger btn-xs pull-right" type="submit" onclick="return confirm('Are you sure you want to delete this company?')"><i class="fa fa-trash"></i> Delete</button>
                                    </form>
                                    <a class="btn btn-default btn-xs pull-right raw-margin-right-16" href="{!! route('companies.edit', [$company->id]) !!}"><i class="fa fa-pencil"></i> Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-center">
            {!! $companies; !!}
        </div>
    </div>

@stop
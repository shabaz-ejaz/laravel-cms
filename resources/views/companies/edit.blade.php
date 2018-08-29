@extends('cms::layouts.dashboard', ['pageTitle' => 'Companies &raquo; Edit'])

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right raw-margin-top-24 raw-margin-left-24">
                {!! Form::open(['route' => 'companies.search']) !!}
                <input class="form-control form-inline pull-right" name="search" placeholder="Search">
                {!! Form::close() !!}
            </div>
            <h1 class="pull-left">Companies: Edit</h1>
            <a class="btn btn-primary pull-right raw-margin-top-24 raw-margin-right-8" href="{!! route('companies.create') !!}">Add New</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">


            {!! Form::model($company, ['route' => ['companies.update', $company->id], 'method' => 'patch']) !!}

            {!! FormMaker::fromObject($company,
            [
                'name',
                'description' => [
                    'type' => 'textarea'
                ],
                'industry' => [
                    'type' => 'select',
                    'alt_name' => 'Industry',
                    'options' => $industries
                ],

                'number_of_staff' => [
                    'type' => 'number'
                ],
                'actual_number_of_staff' => [
                    'type' => 'number',
                    'custom' => 'readonly="true"'
                ],
                'active' => [
                    'type' => 'checkbox',
                    'alt_name' => 'Active'
                ]



             ]) !!}

            {!! Form::submit('Update', ['class' => 'btn btn-primary pull-right']) !!}

            {!! Form::close() !!}

        </div>
    </div>

@stop

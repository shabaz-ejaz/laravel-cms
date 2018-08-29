@extends('cms::layouts.dashboard', ['pageTitle' => 'Companies &raquo; Create'])

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right raw-margin-top-24 raw-margin-left-24">
                {!! Form::open(['route' => 'companies.search']) !!}
                <input class="form-control form-inline pull-right" name="search" placeholder="Search">
                {!! Form::close() !!}
            </div>
            <h1 class="pull-left">Companies: Create</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

            {!! Form::open(['route' => 'companies.store']) !!}


            {!! FormMaker::fromTable('companies', [

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
                'active' => [
                    'type' => 'checkbox',
                    'alt_name' => 'Active',
                ]

            ])

            !!}



            {!! Form::submit('Create', ['class' => 'btn btn-primary pull-right']) !!}

            {!! Form::close() !!}

        </div>
    </div>

@stop
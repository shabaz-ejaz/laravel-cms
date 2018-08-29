@extends('dashboard', ['pageTitle' => 'Posts &raquo; Edit'])

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right raw-margin-top-24 raw-margin-left-24">
                {!! Form::open(['route' => 'posts.search']) !!}
                <input class="form-control form-inline pull-right" name="search" placeholder="Search">
                {!! Form::close() !!}
            </div>
            <h1 class="pull-left">Posts: Edit</h1>
            <a class="btn btn-primary pull-right raw-margin-top-24 raw-margin-right-8" href="{!! route('posts.create') !!}">Add New</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            {!! Form::model($post, ['route' => ['posts.update', $post->id], 'method' => 'patch']) !!}

            @form_maker_object($post, FormMaker::getTableColumns('posts'))

            {!! Form::submit('Update', ['class' => 'btn btn-primary pull-right']) !!}

            {!! Form::close() !!}

        </div>
    </div>

@stop

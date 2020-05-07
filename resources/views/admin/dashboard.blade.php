@extends('layouts.master')

@section('title', 'Admin')
<style>
    label{
        width:100px;
    }
</style>
@section('content')
    <div class="row">
        <div class="col-lg-6 col-sm-10 col-xs-12">
            <div class="white-box">
                <div>
                {{ Form::open(array('url' => '/admin','method'=>'put')) }}
                <!-- if there are login errors, show them here -->
                    <p class="error">
                        {{ $errors->first('authorization') }}
                    </p>
                    <p>
                        {{ Form::label('authorization', 'Authorization ') }}
                        {{ Form::select ('authorization', $authDrivers, $authDriversSelected) }}
                    </p>
                    <p>
                    </p>

                    <p>{{ Form::submit('Change config') }}</p>
                    {{ Form::close() }}
                </div>

            </div>
        </div>
    </div>
@stop

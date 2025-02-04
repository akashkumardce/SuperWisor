@extends('layouts.master')

@section('title', 'User')

@section('content')
    <div class="row">
        <!-- .col -->
        <div class="col-md-14 col-lg-8 col-sm-12">
            <div class="white-box">
                <h3 class="box-title">Team</h3>
                <div class="table-responsive">
                    <table class="user-table select-checkbox stripe">
                        <thead>
                        <tr>
                            <th>NAME</th>
                            <th>ROLE</th>
                            <th>TEAMS</th>
                            <th>MASTER</th>
                            <th>USER</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="white-box">
                <h3 class="box-title"></h3>
                <div class="table-responsive">
                {{ Form::open(array('url' => '/users','method'=>'post')) }}

                <!-- if there are login errors, show them here -->
                    <p class="error">
                        {{ $errors->first('email') }}
                    </p>
                    <p>
                        {{ Form::label('email', 'Email ') }}
                        {{ Form::text('email') }}
                    </p>
                    <p class="error">
                        {{ $errors->first('name') }}
                    </p>
                    <p>
                        {{ Form::label('name', 'Name ') }}
                        {{ Form::text('name') }}
                    </p>
                    <p class="error">
                        {{ $errors->first('role') }}
                    </p>
                    <p>
                        {{ Form::label('role', 'Role ') }}
                        {{ Form::select ('role', $roles) }}
                    </p>
                    <p class="error">
                        {{ $errors->first('password') }}
                    </p>
                    <p>
                        {{ Form::label('password', 'Password ') }}
                        {{ Form::password('password') }}
                    </p>

                    <p>{{ Form::submit('Add New User',['class'=>'btn btn-info pull-right m-r-15 m-t-15 m-l-10 hidden-xs hidden-sm waves-effect waves-light']) }}</p>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="panel log-panel">
                <div class="sk-chat-widgets">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Recent Activity
                        </div>
                        <div class="panel-body">
                            <table class="log-table">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <script>
        var usertable,logtable;
        $(document).ready(function () {
            usertable = $('.user-table').DataTable({
                processing: true,
                bLengthChange: false,
                serverSide: true,
                ordering: false,
                ajax: "{{ url('/user-list') }}",
                columns: [
                    {
                        data: 'name', name: 'name', searchable: true,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/users") }}/' + row.id + '">' + row.name + '</a>'; // row object contains the row data
                            return a;
                        }
                    },
                    {data: 'creator.name', name: 'created_by', searchable: false},
                    {
                        data: 'id', name: 'total', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = parseInt(row.master_count)+parseInt(row.developer_count);
                            return a;
                        }
                    },
                    {data: 'master_count', name: 'member', searchable: false},
                    {data: 'developer_count', name: 'master', searchable: false},
                    {
                        data: 'id', name: 'action', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/users") }}/' + row.id + '"> <i class="fa fa-edit"></i></a>'; // row object contains the row data
                            return a;
                        }
                    },
                    {
                        data: 'id', name: 'remove', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="javascript:void(0)" onclick="removeUser('+row.id+',\''+row.name+'\')"><i class="fa fa-trash"></i> </a> ';
                            return a;
                        }
                    }

                ]
            });
        });


        $(document).ready(function () {
            logtable = $('.log-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: false,
                serverSide: true,
                paging: false,
                ordering: false,
                ajax: "{{ url('/log-list/user') }}",
                columns: [
                    {data: 'user.name', name: 'user'},
                    {data: 'message', name: 'message'},
                ]
            });
        });

        function removeUser(id,value) {
            $.ajax({
                type:'DELETE',
                url:"{{ url('/users/') }}/"+id,
                data:{id:id,value:value,"_token": "{{ csrf_token() }}"},
                success:function(data) {
                    $("#msg").html(data.message);
                    usertable.ajax.reload();
                    logtable.ajax.reload();

                    if(data.hasOwnProperty('success')) {
                        var title = 'User removed Successfuly';
                        var message = data.success;
                        success(title,message);
                    }else{
                        var title = 'User removal Failed';
                        var message = data.error;
                        error(title,message);
                    }

                },
                error: function(xhr, msg){
                    var title = 'User removal Failed';
                    error(title,msg);
                }
            });
        }
    </script>
@stop

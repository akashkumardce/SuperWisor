@extends('layouts.master')

@section('title', 'Server')

@section('content')
    <div class="row">
        <!-- .col -->
        <div class="col-md-14 col-lg-8 col-sm-12">
            <?php if(session()->has('success')): ?>
            <div class="alert alert-success">
                <?php echo e(session()->get('success')); ?>

            </div>
            <?php endif; ?>
            <div class="white-box">
                <h3 class="box-title">Team</h3>
                <div class="table-responsive">
                    <table class="server-table select-checkbox stripe">
                        <thead>
                        <tr>
                            <th>IP</th>
                            <th>CREATOR</th>
                            <th>SERVICES</th>
                            <th>RUNNING</th>
                            <th>ERRORS</th>
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
                {{ Form::open(array('url' => '/servers','method'=>'post')) }}

                <!-- if there are login errors, show them here -->
                    <p class="error">
                        {{ $errors->first('ip') }}
                    </p>
                    <p>
                        {{ Form::label('ip', 'IP ') }}
                        {{ Form::textarea('ip',null,[
                        'placeholder'=>'Comma Separated IP addresses',
                        'cols'=>20,
                        'rows'=>5]) }}
                    </p>
                    <p class="error">
                        {{ $errors->first('password') }}
                    </p>
                    <p>
                        {{ Form::label('password', 'Password ') }}
                        {{ Form::password('password') }}
                    </p>

                    <p>{{ Form::submit('Add New Servers',['class'=>'btn btn-info pull-right m-r-15 m-t-15 m-l-10 hidden-xs hidden-sm waves-effect waves-light']) }}</p>
                    {{ Form::close() }}
                </div>
            </div>
            <div class="white-box">
                <h3 class="box-title">FAQ to add servers-</h3>
                <div class="table-responsive">
                    <ol>
                        <li>Install supervisord on server</li>
                        <li>Change supervisor.conf - <a href="http://supervisord.org/configuration.html#inet-http-server-section-settings">refer</a>
                            <ol>
                                <li><b>Username</b> - superwisor</li>
                                <li><b>Port</b> - 9001</li>
                                <li><b>Password</b> - The password required for authentication to this HTTP server. This can be a cleartext password, or can be specified as a SHA-1 hash if prefixed by the string {SHA}. For example, {SHA}82ab876d1387bfafe46cc1c8a2ef074eae50cb1d is the SHA-stored version of the password “thepassword”.</li>
                            </ol>
                        </li>
                    </ol>
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
        var servertable,logtable;
        $(document).ready(function () {
            servertable = $('.server-table').DataTable({
                processing: true,
                bLengthChange: false,
                serverSide: true,
                ordering: false,
                ajax: "{{ url('/server-list') }}",
                columns: [
                    {
                        data: 'name', name: 'name', searchable: true,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/servers") }}/' + row.id + '">' + row.name + '</a>'; // row object contains the row data
                            return a;
                        }
                    },
                    {data: 'creator.name', name: 'created_by', searchable: false},
                    {data: 'service_count', name: 'service_count', searchable: false},
                    {data: 'running_count', name: 'running_count', searchable: false},
                    {data: 'error_count', name: 'error_count', searchable: false},
                    {
                        data: 'id', name: 'action', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/servers") }}/' + row.id + '"> <i class="fa fa-edit"></i></a>'; // row object contains the row data
                            return a;
                        }
                    },
                    {
                        data: 'id', name: 'remove', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="javascript:void(0)" onclick="removeServer('+row.id+',\''+row.name+'\')"><i class="fa fa-trash"></i> </a> ';
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
                ajax: "{{ url('/log-list/server') }}",
                columns: [
                    {data: 'user.name', name: 'user'},
                    {data: 'message', name: 'message'},
                ]
            });
        });

        function removeServer(id,value) {
            $.ajax({
                type:'DELETE',
                url:"{{ url('/servers/') }}/"+id,
                data:{id:id,value:value,"_token": "{{ csrf_token() }}"},
                success:function(data) {
                    $("#msg").html(data.message);
                    servertable.ajax.reload();
                    logtable.ajax.reload();

                    if(data.hasOwnProperty('success')) {
                        var title = 'Server removed Successfuly';
                        var message = data.success;
                        success(title,message);
                    }else{
                        var title = 'Server removal Failed';
                        var message = data.error;
                        error(title,message);
                    }

                },
                error: function(xhr, msg){
                    var title = 'Server removal Failed';
                    error(title,msg);
                }
            });
        }
    </script>
@stop

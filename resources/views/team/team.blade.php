@extends('layouts.master')

@section('title', 'Team')

@section('content')
    <div class="row">
        <!-- .col -->
        <div class="col-md-12 col-sm-12">
            <div class="white-box">
                <h3 class="box-title">{{ $name }}</h3>
                <div class="basic">
                    <div>Master : <span>{{ $name }}</span></div>
                    <div>Created : <span>{{ $created_at }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="panel m-b-10 p-10 font-12 " style="height: 50px;">
                Master user has access to all the servers and services in the team
            </div>

            <div class="panel log-panel">
                <div class="sk-chat-widgets">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            MASTER
                        </div>
                        <div class="panel-body panel-datatable">
                            <table class="master-table display log-table">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel" style="height: 50px;">
                <div class="col-md-7 m-t-5 m-l-5" style="border: 1px solid;">
                    {{ Form::select ('master', $userList, null , ['class' =>'form-control custom-select b-none']) }}
                </div>
                <div>
                    <a href="javascript:void(0);" onclick="linkMaster()"
                       class="btn btn-info  pull-right m-r-5 m-t-10 m-l-0 hidden-xs hidden-sm waves-effect waves-light">Assign
                        Master</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="panel m-b-10 p-10 font-12 " style="height: 50px;">
                User has view access to all the servers and <b>Stop or restart</b> access to owned services in the team
            </div>
            <div class="panel log-panel">
                <div class="sk-chat-widgets">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            USER
                        </div>
                        <div class="panel-body panel-datatable">
                            <table class="user-table display log-table">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel m-t-10" style="height: 50px;">
                <div class="col-md-7 m-t-5 m-l-5" style="border: 1px solid;">
                    {{ Form::select ('user', $userList, null , ['class' =>'form-control custom-select b-none']) }}
                </div>
                <div>
                    <a href="javascript:void(0);" onclick="linkDeveloper()"
                       class="btn btn-info  pull-right m-r-5 m-t-10 m-l-0 hidden-xs hidden-sm waves-effect waves-light">Assign
                        Users</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="panel m-b-10 p-10 font-12 " style="height: 50px;">
                Any user in the group can watch these server
            </div>

            <div class="panel log-panel">
                <div class="sk-chat-widgets">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Servers
                        </div>
                        <div class="panel-body panel-datatable">
                            <table class="server-table display log-table">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel m-t-10" style="height: 50px;">
                <div class="col-md-7 m-t-5 m-l-5" style="border: 1px solid;">
                    {{ Form::select ('server', $serverList, null , ['class' =>'form-control custom-select pull-right row b-none']) }}
                </div>

                <div>
                    <a href="javascript:void(0);" onclick="linkServer()"
                       class="btn btn-info  pull-right m-r-5 m-t-5 m-l-10 hidden-xs hidden-sm waves-effect waves-light">Link
                        Server</a>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-6 col-sm-12">
            <div class="panel log-panel">
                <div class="sk-chat-widgets">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Logs
                        </div>
                        <div class="panel-body">
                            <table class="activity-table log-table">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <script>
        var mastertable;
        var usertable;
        var servertable
        $(document).ready(function () {
            usertable = $('.user-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: true,
                serverSide: true,
                paging: true,
                ordering: false,
                oLanguage: {sSearch: ""},
                dom: ' <"search"fl><"top">rt<"bottom"ip><"clear">',
                ajax: "{{ url('/teams/') }}/{{ $id  }}/users",
                columns: [
                    {data: 'user.name', name: 'user.user'},
                    {data: 'user.email', name: 'user.email'},
                    {data: 'id', name: 'action',searchable:false,
                        'render': function(data,type,row,meta) { // render event defines the markup of the cell text
                            var a = '<a href="javascript:void(0)" onclick="removeUser('+row.id+',\''+row.user.email+'\')"><i class="fa fa-trash"></i></a>'; // row object contains the row data
                            return a;
                        }
                    }
                ]
            });
            mastertable = $('.master-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: true,
                serverSide: true,
                paging: true,
                ordering: false,
                oLanguage: {sSearch: ""},
                dom: ' <"search"fl><"top">rt<"bottom"ip><"clear">',
                ajax: "{{ url('/teams/') }}/{{ $id  }}/masters",
                columns: [
                    {data: 'user.name', name: 'user.name'},
                    {data: 'user.email', name: 'user.email'},
                    {data: 'id', name: 'action',searchable:false,
                        'render': function(data,type,row,meta) { // render event defines the markup of the cell text
                            var a = '<a href="javascript:void(0)" onclick="removeMaster('+row.id+',\''+row.user.email+'\')"><i class="fa fa-trash"></i></a>'; // row object contains the row data
                            return a;
                        }
                    }
                ]
            });
            servertable = $('.server-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: true,
                serverSide: true,
                paging: true,
                ordering: false,
                oLanguage: {sSearch: ""},
                ajax: "{{ url('/teams/') }}/{{ $id  }}/servers",
                columns: [
                    {data: 'server.name', name: 'server.name'},
                    {data: 'server.ip', name: 'server.ip'},
                    {data: 'id', name: 'action',searchable:false,
                        'render': function(data,type,row,meta) { // render event defines the markup of the cell text
                            var a = '<a href="javascript:void(0)" onclick="removeServer('+row.id+',\''+row.server.ip+'\')"><i class="fa fa-trash"></i></a>'; // row object contains the row data
                            return a;
                        }
                    }
                ]
            });
            var logtable = $('.activity-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: false,
                serverSide: true,
                paging: false,
                ordering: false,
                ajax: "{{ url('/log-list/team') }}{{ $id  }}",
                columns: [
                    {data: 'user.name', name: 'user'},
                    {data: 'message', name: 'message'},
                ]
            });
        });

        function removeMaster(id,value) {
            $.ajax({
                type:'DELETE',
                url:"{{ url('/teams/') }}/{{ $id  }}/masters",
                data:{id:id,value:value,"_token": "{{ csrf_token() }}"},
                success:function(data) {
                    $("#msg").html(data.message);
                    mastertable.ajax.reload();

                    if(data.hasOwnProperty('success')) {
                        var title = 'Master profile removed Successfuly';
                        var message = data.success;
                        success(title,message);
                    }else{
                        var title = 'Master profile removal Failed';
                        var message = data.error;
                        error(title,message);
                    }

                },
                error: function(xhr, msg){
                    var title = 'Master profile removal Failed';
                    error(title,msg);
                }
            });
        }
        function removeUser(id,value) {
            $.ajax({
                type:'DELETE',
                url:"{{ url('/teams/') }}/{{ $id  }}/users",
                data:{id:id,value:value,"_token": "{{ csrf_token() }}"},
                success:function(data) {
                    $("#msg").html(data.message);
                    usertable.ajax.reload();

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
        function removeServer(id,value) {
            $.ajax({
                type:'DELETE',
                url:"{{ url('/teams/') }}/{{ $id  }}/servers",
                data:{id:id,value:value,"_token": "{{ csrf_token() }}"},
                success:function(data) {
                    $("#msg").html(data.message);
                    servertable.ajax.reload();

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

        function linkMaster() {
            var selectedValue = $('select[name="master"]').val();
            var selectedText = $( 'select[name="master"] option:selected').text();
            $.ajax({
                type:'POST',
                url:"{{ url('/teams/') }}/{{ $id  }}/masters",
                data:{id:selectedValue,value:selectedText,"_token": "{{ csrf_token() }}"},
                success:function(data) {
                    $("#msg").html(data.message);
                    mastertable.ajax.reload();

                    if(data.hasOwnProperty('success')) {
                        var title = 'Master profile added Successfuly';
                        var message = data.success;
                        success(title,message);
                    }else{
                        var title = 'Master profile addition Failed';
                        var message = data.error;
                        error(title,message);
                    }

                },
                error: function(xhr, msg){
                    var title = 'Master profile addition Failed';
                    error(title,msg);
                }
            });
        }

        function linkDeveloper() {
            var selectedValue = $('select[name="user"]').val();
            var selectedText = $( 'select[name="master"] option:selected').text();
            $.ajax({
                type:'POST',
                url:"{{ url('/teams/') }}/{{ $id  }}/users",
                data:{id:selectedValue,value:selectedText,"_token": "{{ csrf_token() }}"},
                success:function(data) {
                    $("#msg").html(data.message);
                    usertable.ajax.reload();

                    if(data.hasOwnProperty('success')) {
                        var title = 'Developer profile added Successfuly';
                        var message = data.success;
                        success(title,message);
                    }else{
                        var title = 'Developer profile addition Failed';
                        var message = data.error;
                        error(title,message);
                    }

                },
                error: function(xhr, msg){
                    var title = 'Developer profile addition Failed';
                    error(title,msg);
                }
            });
        }

        function linkServer() {
            var selectedValue = $('select[name="server"]').val();
            var selectedText = $( 'select[name="master"] option:selected').text();
            $.ajax({
                type:'POST',
                url:"{{ url('/teams/') }}/{{ $id  }}/servers",
                data:{id:selectedValue,value:selectedText,"_token": "{{ csrf_token() }}"},
                success:function(data) {
                    $("#msg").html(data.message);
                    servertable.ajax.reload();

                    if(data.hasOwnProperty('success')) {
                        var title = 'Server added Successfuly';
                        var message = data.success;
                        success(title,message);
                    }else{
                        var title = 'Server addition Failed';
                        var message = data.error;
                        error(title,message);
                    }

                },
                error: function(xhr, msg){
                    var title = 'Server addition Failed';
                    error(title,msg);
                }
            });
        }
    </script>
@stop

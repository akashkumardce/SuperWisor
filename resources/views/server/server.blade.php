@extends('layouts.master')

@section('title', "Server - $name")

@section('content')
    <div class="row">
        <!-- .col -->
        <div class="col-md-12 col-sm-12">
            @if(session()->has('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div>
            @endif
            <p class="error">
                {{ $errors->first('password') }}
            </p>
            <div class="white-box">
                <h3 class="box-title">{{ $name }}</h3>
                <div class="basic">
                    <div class="pull-right option-button">
                        <a href="javascript:void(0)" onclick="editServer('{{ $id }}','{{ $name }}')"><i class="fa fa-user-secret"></i> Edit Password</a><br>
                        <a href="javascript:void(0)" onclick="removeServer('{{ $id }}','{{ $name }}')"><i class="fa fa-trash"></i> Remove Server</a>
                    </div>
                    <div class="pull-right option-boxing hidden">
                        {{ Form::open(array('url' => '/servers/'.$id,'method'=>'put')) }}
                        Change password to
                        {{ Form::password ('password') }}<br><br>
                        {{ Form::submit('change') }}
                        {{ Form::close() }}
                    </div>


                    <div>Created : <span>by {{ $created_by }} at {{ $created_at }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="panel m-b-10 p-10 font-12 " style="height: 50px;">
                Teams working on this server
            </div>

            <div class="panel log-panel">
                <div class="sk-chat-widgets">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Teams
                        </div>
                        <div class="panel-body panel-datatable">
                            <table class="team-table display log-table">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="panel m-b-10 p-10 font-12 " style="height: 50px;">
                Services Parsed during last status check
            </div>
            <div class="panel log-panel">
                <div class="sk-chat-widgets">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Services
                        </div>
                        <div class="panel-body panel-datatable">
                            <table class="service-table display log-table">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
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
        var servicetable;
        var teamtable;
        var logtable;
        $(document).ready(function () {
            teamtable = $('.team-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: true,
                serverSide: true,
                paging: true,
                ordering: false,
                oLanguage: {sSearch: ""},
                dom: ' <"search"fl><"top">rt<"bottom"ip><"clear">',
                ajax: "{{ url('/servers/') }}/{{ $id  }}/team",
                columns: [
                    {
                        data: 'team.name', name: 'name', searchable: true,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/teams") }}/' + row.team_id + '">' + row.team.name + '</a>'; // row object contains the row data
                            return a;
                        }
                    },
                    {
                        data: 'team.name', name: 'action', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="javascript:void(0)" onclick="removeTeam('+row.id+',\''+row.team.name+'\')"><i class="fa fa-trash"></i></a>'; // row object contains the row data
                            return a;
                        }
                    }
                ]
            });
            servicetable = $('.service-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: true,
                serverSide: true,
                paging: true,
                ordering: false,
                oLanguage: {sSearch: ""},
                dom: ' <"search"fl><"top">rt<"bottom"ip><"clear">',
                ajax: "{{ url('/servers/') }}/{{ $id  }}/service",
                columns: [
                    {
                        data: 'service.name', name: 'name', searchable: true,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/services") }}/' + row.service.id + '">' + row.service.name + '</a>'; // row object contains the row data
                            return a;
                        }
                    }
                ]
            });
            logtable = $('.activity-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: false,
                serverSide: true,
                paging: false,
                ordering: false,
                ajax: "{{ url('/log-list/server') }}{{ $id  }}",
                columns: [
                    {data: 'user.name', name: 'user'},
                    {data: 'message', name: 'message'},
                ]
            });
        });


        function removeTeam(id,value) {
            $.ajax({
                type:'DELETE',
                url:"{{ url('/servers/') }}/{{ $id  }}/team",
                data:{id:id,value:value,"_token": "{{ csrf_token() }}"},
                success:function(data) {
                    $("#msg").html(data.message);
                    servicetable.ajax.reload();
                    teamtable.ajax.reload();
                    logtable.ajax.reload();

                    if(data.hasOwnProperty('success')) {
                        var title = 'Team unlinked Successfuly';
                        var message = data.success;
                        success(title,message);
                    }else{
                        var title = 'Team unlinking failed';
                        var message = data.error;
                        error(title,message);
                    }

                },
                error: function(xhr, msg){
                    var title = 'Team unlinking failed';
                    error(title,msg);
                }
            });
        }


        function removeServer(id,value) {
            $.ajax({
                type:'DELETE',
                url:"{{ url('/servers/') }}/"+id,
                data:{id:id,value:value,"_token": "{{ csrf_token() }}"},
                success:function(data) {
                    $("#msg").html(data.message);
                    servicetable.ajax.reload();
                    teamtable.ajax.reload();
                    logtable.ajax.reload();

                    if(data.hasOwnProperty('success')) {
                        var title = 'Server removed Successfuly';
                        var message = data.success;
                        success(title,message);
                        setTimeout(function () {
                            window.location.reload();
                        },3000);
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

        function editServer(id,value){
            $(".option-button").hide();
            $(".option-boxing").show().removeClass('hidden');

        }


    </script>
@stop

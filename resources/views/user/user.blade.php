@extends('layouts.master')

@section('title', 'User')

@section('content')
    <div class="row">
        <!-- .col -->
        <div class="col-md-12 col-sm-12">
            <p class="error">
                {{ $errors->first('role') }}
            </p>
            <div class="white-box">
                <h3 class="box-title">{{ $name }}</h3>
                <div class="basic">
                    <div>Role : <span><b>{{ $role }}</b></span></div>
                    <div class="pull-right">
                        --- OR --- <a href="javascript:void(0)" onclick="removeUser('{{ $id }}','{{ $name }}')"><i class="fa fa-trash"></i> Remove User</a>
                    </div>
                    <div class="pull-right option-box">Change role to
                        {{ Form::open(array('url' => '/users/'.$id,'method'=>'put')) }}
                        {{ Form::select ('role', $roleOptions) }}<br><br>
                        {{ Form::submit('change') }}
                        {{ Form::close() }}
                    </div>

                    <div>Email : <span>{{ $email }}</span>
                    </div>


                    <div>Created : <span>by {{ $created_by }} at {{ $created_at }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="panel m-b-10 p-10 font-12 " style="height: 50px;">
                Teams in which user has Master access.
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
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="panel m-b-10 p-10 font-12 " style="height: 50px;">
                Teams in which user has Developer access. i.e. limited to selected service access
            </div>
            <div class="panel log-panel">
                <div class="sk-chat-widgets">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            DEVELOPER
                        </div>
                        <div class="panel-body panel-datatable">
                            <table class="user-table display log-table">

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
                ajax: "{{ url('/users/') }}/{{ $id  }}/dev-team",
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
            mastertable = $('.master-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: true,
                serverSide: true,
                paging: true,
                ordering: false,
                oLanguage: {sSearch: ""},
                dom: ' <"search"fl><"top">rt<"bottom"ip><"clear">',
                ajax: "{{ url('/users/') }}/{{ $id  }}/master-team",
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
            var logtable = $('.activity-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: false,
                serverSide: true,
                paging: false,
                ordering: false,
                ajax: "{{ url('/log-list/user') }}{{ $id  }}",
                columns: [
                    {data: 'user.name', name: 'user'},
                    {data: 'message', name: 'message'},
                ]
            });
        });


        function removeTeam(id,value) {
            $.ajax({
                type:'DELETE',
                url:"{{ url('/users/') }}/{{ $id  }}/team",
                data:{id:id,value:value,"_token": "{{ csrf_token() }}"},
                success:function(data) {
                    $("#msg").html(data.message);
                    mastertable.ajax.reload();
                    usertable.ajax.reload();
                    logtable.ajax.reload();

                    if(data.hasOwnProperty('success')) {
                        var title = 'User from Team removed Successfuly';
                        var message = data.success;
                        success(title,message);
                    }else{
                        var title = 'User from Team removal Failed';
                        var message = data.error;
                        error(title,message);
                    }

                },
                error: function(xhr, msg){
                    var title = 'User from Team removal Failed';
                    error(title,msg);
                }
            });
        }

        function removeUser(id,value) {
            $.ajax({
                type:'DELETE',
                url:"{{ url('/users/') }}/{{ $id  }}",
                data:{id:id,value:value,"_token": "{{ csrf_token() }}"},
                success:function(data) {
                    $("#msg").html(data.message);


                    if(data.hasOwnProperty('success')) {
                        var title = 'User removed Successfuly';
                        var message = data.success;
                        success(title,message);
                        setTimeout(function () {
                            window.location.href="/user-manage";
                        },3000);
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

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
            <div class="white-box">
                <h3 class="box-title">{{ $name }}</h3>
                <div class="pull-right col-lg-3 col-md-6 col-sm-12" style="margin-top:-70px;">
                    @if($isMaster)
                        <div class="panel m-t-5" style="height: 90px;">
                            <div class="col-md-11 m-t-5 m-l-5" style="border: 1px solid;">
                                {{ Form::select ('user', $userList, null , ['class' =>'form-control custom-select pull-right row b-none']) }}
                            </div>

                            <div>
                                <a href="javascript:void(0);" onclick="linkDeveloper()"
                                   class="btn btn-info col-md-11  m-r-5 m-t-5 m-l-5 hidden-xs hidden-sm waves-effect waves-light">
                                    Add Developer
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="panel m-b-10 p-10 font-12 " style="height: 60px;">
                Servers on which service is running on
            </div>

            <div class="panel log-panel">
                <div class="sk-chat-widgets">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Servers IP
                        </div>
                        <div class="panel-body panel-datatable">
                            <table class="server-table display log-table">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="panel m-b-10 p-10 font-12 " style="height: 60px;">
                Master have permission to start, stop, restart services<br>
                Master can add developers in service
            </div>

            <div class="panel log-panel">
                <div class="sk-chat-widgets">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Masters Users
                        </div>
                        <div class="panel-body panel-datatable">
                            <table class="master-table display log-table">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="panel m-b-5 p-10 font-12 " style="height: 60px;">
                - Developers are users from Team<br>
                - Developer have permission to start, stop, restart services
            </div>

            <div class="panel log-panel">
                <div class="sk-chat-widgets">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Developer Users
                        </div>
                        <div class="panel-body panel-datatable">
                            <table class="developer-table display log-table">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="panel m-b-10 p-10 font-12 " style="height: 100px;">
                - All users of Teams are Watcher and have have permission to view status of services<br>
                - This team list is dependent on <b>servers</b>
            </div>

            <div class="panel log-panel">
                <div class="sk-chat-widgets">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Watcher Team
                        </div>
                        <div class="panel-body panel-datatable">
                            <table class="watcher-table display log-table">

                            </table>
                        </div>
                    </div>
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
        var servicetable;
        var teamtable;
        var logtable;
        var developertable, watchertable;
        $(document).ready(function () {
            teamtable = $('.master-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: true,
                serverSide: true,
                paging: true,
                ordering: false,
                oLanguage: {sSearch: ""},
                dom: ' <"search"fl><"top">rt<"bottom"ip><"clear">',
                ajax: "{{ url('/services/') }}/{{ $id  }}/master-team",
                columns: [
                    {
                        data: 'user.email', name: 'user.email', searchable: true,
                        @if($isMaster && Auth::user()->role == 'ADMIN')
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/users") }}/' + row.user.id + '">' + row.user.email + '</a>'; // row object contains the row data
                            return a;
                        }
                        @endif
                    }
                ]
            });
            developertable = $('.developer-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: true,
                serverSide: true,
                paging: true,
                ordering: false,
                oLanguage: {sSearch: ""},
                dom: ' <"search"fl><"top">rt<"bottom"ip><"clear">',
                ajax: "{{ url('/services/') }}/{{ $id  }}/developer-team",
                columns: [
                    {
                        data: 'user.email', name: 'user.email', searchable: true,
                        @if($isMaster && Auth::user()->role == 'ADMIN')
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/users") }}/' + row.user.id + '">' + row.user.email + '</a>'; // row object contains the row data
                            return a;
                        }
                        @endif
                    },
                    {
                        data: 'user.email', name: 'action', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '';
                            @if($isMaster)
                                a += '<a href="javascript:void(0)" onclick="removeDev(' + row.id + ',\'' + row.user.email + '\')"><i class="fa fa-trash"></i></a>'; // row object contains the row data
                            @endif
                                return a;
                        }
                    }
                ]
            });
            watchertable = $('.watcher-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: true,
                serverSide: true,
                paging: true,
                ordering: false,
                oLanguage: {sSearch: ""},
                dom: ' <"search"fl><"top">rt<"bottom"ip><"clear">',
                ajax: "{{ url('/services/') }}/{{ $id  }}/team",
                columns: [
                    {
                        data: 'name', name: 'name', searchable: true,
                        @if($isMaster && Auth::user()->role == 'ADMIN')
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/teams") }}/' + row.id + '">' + row.name + '</a>'; // row object contains the row data
                            return a;
                        }
                        @endif
                    }
                ]
            });
            servicetable = $('.server-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: true,
                serverSide: true,
                paging: true,
                ordering: false,
                oLanguage: {sSearch: ""},
                dom: ' <"search"fl><"top">rt<"bottom"ip><"clear">',
                ajax: "{{ url('/services/') }}/{{ $id  }}/server",
                columns: [
                    {
                        data: 'server.ip', name: 'server.ip', searchable: true,
                        @if($isMaster && Auth::user()->role == 'ADMIN')
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/servers") }}/' + row.server.id + '">' + row.server.ip + '</a>'; // row object contains the row data
                            return a;
                        }
                        @endif
                    },
                    {data: 'status', name: 'status', searchable: false},
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


        function removeDev(id, value) {
            $.ajax({
                type: 'DELETE',
                url: "{{ url('/services/') }}/{{ $id  }}/developer",
                data: {id: id, value: value, "_token": "{{ csrf_token() }}"},
                success: function (data) {
                    $("#msg").html(data.message);
                    developertable.ajax.reload();
                    logtable.ajax.reload();

                    if (data.hasOwnProperty('success')) {
                        var title = 'Developer unlinked Successfuly';
                        var message = data.success;
                        success(title, message);
                    } else {
                        var title = 'Developer unlinking failed';
                        var message = data.error;
                        error(title, message);
                    }

                },
                error: function (xhr, msg) {
                    var title = 'Developer unlinking failed';
                    error(title, msg);
                }
            });
        }

        function linkDeveloper() {
            var selectedValue = $('select[name="user"]').val();
            var selectedText = $('select[name="user"] option:selected').text();
            $.ajax({
                type: 'POST',
                url: "{{ url('/services/') }}/{{ $id  }}/developer",
                data: {id: selectedValue, value: selectedText, "_token": "{{ csrf_token() }}"},
                success: function (data) {
                    $("#msg").html(data.message);
                    developertable.ajax.reload();
                    logtable.ajax.reload();

                    if (data.hasOwnProperty('success')) {
                        var title = 'Developer profile linked Successfuly';
                        var message = data.success;
                        success(title, message);
                    } else {
                        var title = 'Developer profile linked Failed';
                        var message = data.error;
                        error(title, message);
                    }

                },
                error: function (xhr, msg) {
                    var title = 'Developer profile linked Failed';
                    error(title, msg);
                }
            });
        }


    </script>
@stop

@extends('layouts.master')

@section('title', 'Server')

@section('content')
    <div class="row">
        <!-- .col -->
        <div class="col-md-14 col-lg-8 col-sm-12">
            <div class="white-box">
                <h3 class="box-title">Server</h3>
                <div class="table-responsive">
                    <table class="service-table select-checkbox stripe">
                        <thead>
                        <tr>
                            <th>SERVICE</th>
                            <th>RUNNING INSTANCES</th>
                        </tr>
                        </thead>

                    </table>
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
        var servicetable,logtable;
        $(document).ready(function () {
            servicetable = $('.service-table').DataTable({
                processing: true,
                bLengthChange: false,
                serverSide: true,
                ordering: false,
                ajax: "{{ url('/service-list') }}",
                columns: [
                    {
                        data: 'name', name: 'name', searchable: true,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/services") }}/' + row.id + '">' + row.name + '</a>'; // row object contains the row data
                            return a;
                        }
                    },
                    {
                        data: 'running_count', name: 'serviceCoutn', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a =  row.running_count + ' running out of ' + row.server_count + " servers"; // row object contains the row data
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
                ajax: "{{ url('/log-list/service') }}",
                columns: [
                    {data: 'user.name', name: 'user'},
                    {data: 'message', name: 'message'},
                ]
            });
        });

    </script>
@stop

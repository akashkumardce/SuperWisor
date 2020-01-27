@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-4 col-sm-6 col-xs-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">Total Servers</h3>
                <ul class="list-inline two-part">
                    <li>
                        <div id="sparklinedash"></div>
                    </li>
                    <li class="text-right"><i class="ti-arrow-up text-success"></i> <span
                                class="counter text-success">{{ count($serverList)-1 }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 col-xs-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">Total Services</h3>
                <ul class="list-inline two-part">
                    <li>
                        <div id="sparklinedash2"></div>
                    </li>
                    <li class="text-right"><i class="ti-arrow-up text-purple"></i> <span
                                class="counter text-purple">{{ count($serviceList)-1 }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 col-xs-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">Your Teams</h3>
                <ul class="list-inline two-part">
                    <li>
                        <div id="sparklinedash3"></div>
                    </li>
                    <li class="text-right"><i class="ti-arrow-up text-info"></i> <span
                                class="counter text-info">{{ count($teamList) }}</span></li>
                </ul>
            </div>
        </div>
    </div>
    <!--/.row -->
    <!-- ============================================================== -->
    <div class="row">
        <!-- .col -->
        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="white-box" style="min-height: 300px;">
                <div class="col-md-4 col-sm-4 col-xs-6 pull-right">
                    {{ Form::select ('server', $serverList, null , ['class' =>'form-control pull-right row b-none server-select']) }}
                </div>
                <div class="col-md-1 col-sm-2 col-xs-2 pull-right">
                    <span style="padding: 10px;display: block;">OR</span>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-6 pull-right">
                    {{ Form::select ('service', $serviceList, null , ['class' =>'form-control pull-right row b-none service-select']) }}
                </div>
                <h3 class="box-title title-selected">Live Status</h3>
                <div class="table-responsive data-tables">
                    <table class="server-table">

                    </table>
                    <table class="service-table">

                    </table>
                </div>
                <div class="pull-bottom">
                    Bulk Action : <i class="fa fa-play fa-action"></i> <i class="fa fa-stop fa-action"></i> <i
                            class="fa fa-refresh fa-action"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-6 col-sm-12">
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
        var servertable, logtable, servicetable;
        $(document).ready(function () {

        });
        $('.server-select').on('change', function () {

            var selectedValue = $('select[name="server"]').val();
            var selectedText = $('select[name="server"] option:selected').text();
            $('.service-table').hide();
            $('.server-table').show();
            $('.server-table').html("");
            $('.dataTables_wrapper').remove();
            $('.data-tables').html('<table class="server-table"></table><table class="service-table"></table>');
            if(servertable!=null)
                servertable.destroy();
            if (selectedValue == 0) {
                return;
            }
            $('.service-select').val(0);
            servertable = $('.server-table').DataTable({
                processing: true,
                bLengthChange: false,
                serverSide: true,
                ordering: false,
                ajax: "{{ url('/servers') }}/" + selectedValue + "/service",
                columns: [
                    {
                        data: 'service.name', name: 'service.name', searchable: true,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/services") }}/' + row.service.id + '">' + row.service.name + '</a>'; // row object contains the row data
                            return a;
                        }
                    },
                    {
                        data: 'server.name', name: 'server.name', searchable: true,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/servers") }}/' + row.server.id + '">' + row.server.name + '</a>'; // row object contains the row data
                            return a;
                        }
                    },
                    {
                        data: 'status', name: 'status', searchable: false,
                    },
                    {
                        data: 'server.name', name: 'name', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<i class="fa fa-play fa-action fa-green" data-value="' + row.service.id + '"></i> ' +
                                '<i class="fa fa-stop fa-action fa-red" data-value="' + row.service.id + '"></i> ' +
                                '<i class="fa fa-refresh fa-action  fa-orange" data-value="' + row.service.id + '"></i> ' +
                                '<i class="fa fa-database fa-action  fa-blue" data-value="' + row.service.id + '"></i>'; // row object contains the row data
                            return a;
                        }
                    }

                ]
            });

            $(".title-selected").text(selectedText);

        });

        $('.service-select').on('change', function () {

            var selectedValue = $('select[name="service"]').val();
            var selectedText = $('select[name="service"] option:selected').text();
            $('.service-table').show();
            $('.service-table').html("");
            $('.data-tables').html('<table class="server-table"></table><table class="service-table"></table>');
            $('.dataTables_wrapper').remove();
            if(servicetable!=null)
                servicetable.destroy();

            $('.server-table').hide();
            if (selectedValue == 0) {
                return;
            }
            $('.server-select').val(0);

            servicetable = $('.service-table').DataTable({
                processing: true,
                bLengthChange: false,
                serverSide: true,
                ordering: false,
                ajax: "{{ url('/services') }}/" + selectedValue + "/server",
                columns: [
                    {
                        data: 'service.name', name: 'service.name', searchable: true,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/services") }}/' + row.service.id + '">' + row.service.name + '</a>'; // row object contains the row data
                            return a;
                        }
                    },
                    {
                        data: 'server.name', name: 'server.name', searchable: true,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<a href="' + '{{ url("/servers") }}/' + row.server.id + '">' + row.server.name + '</a>'; // row object contains the row data
                            return a;
                        }
                    },
                    {
                        data: 'status', name: 'status', searchable: false,
                    },
                    {
                        data: 'server.name', name: 'name', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = '<i class="fa fa-play fa-action fa-green" data-value="' + row.service.id + '"></i> ' +
                                '<i class="fa fa-stop fa-action fa-red" data-value="' + row.service.id + '"></i> ' +
                                '<i class="fa fa-refresh fa-action  fa-orange" data-value="' + row.service.id + '"></i> ' +
                                '<i class="fa fa-database fa-action  fa-blue" data-value="' + row.service.id + '"></i>'; // row object contains the row data
                            return a;
                        }
                    }
                ]
            });

            $(".title-selected").text(selectedText);

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

    </script>
@stop

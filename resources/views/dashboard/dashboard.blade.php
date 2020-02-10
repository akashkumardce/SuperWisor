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
        <div class="col-md-8 col-lg-9 col-sm-12">
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
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="panel log-panel">
                <div class="sk-chat-widgets">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Recent Activity
                        </div>
                        <div class="panel-body data-tables-log" style="padding:1px;>
                            <table class="log-table">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- Modal -->
    <div class="modal fade" id="logModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Logs</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body log-body" style="height:300px;overflow-y: scroll;">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
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
            $('.server-table').html('');
            $('.data-tables').html('<table class="service-table"></table><table class="server-table">' +
                '<thead><tr><td></td><td>Service</td><td>Server</td><td>Started(last)</td><td>Stoped(last)</td><td>Status(updated at)</td><td>Action</td></tr></thead></table>');
            $('.dataTables_wrapper').remove();

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
                        data: 'service.name', name: 'service.name', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = ''; // row object contains the row data
                            return a;
                        }
                    },
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
                        data: 'start', name: 'start', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var time = new Date(row.start*1000)
                            var name = (row.startby==null)?"":(row.startby.name);
                            var diff = name+"<br><span class='tinyinfo'>"+timediff(time)+"</span>";
                            return diff;
                        }
                    },
                    {
                        data: 'stop', name: 'stop', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var time = new Date(row.stop*1000)
                            var name = (row.stopby==null)?"":(row.stopby.name);
                            var diff = name+"<br><span class='tinyinfo'>"+timediff(time)+"</span>";
                            return diff;
                        }
                    },
                    {
                        data: 'status', name: 'status', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var checked_at = new Date(row.checked_at)
                            var diff = timediff(checked_at);
                            var a = '<span class="status-'+row.status+'"">'+row.status+'</span>'+
                                '<i class="fa action-button fa-refresh fa-action  fa-orange status-'+row.status+'" title="refresh status" data-action="refresh"  data-service-value="' + row.service.id + '"  data-server-value="' + row.server.id + '"></i> '
                                +"("+diff+")";
                            return a;
                        }
                    },
                    {
                        data: 'status', name: 'status', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a="";
                            if(row.status!="RUNNING") {
                                console.log("----"+row.status);

                                a += '<i class="fa action-button fa-play fa-action fa-green" title="start" data-action="start" data-service-value="' + row.service.id + '"  data-server-value="' + row.server.id + '"></i> ';
                            }
                            if(row.status=="RUNNING") {
                                a += '<i class="fa action-button fa-stop fa-action fa-red"  title="stop" data-action="stop"  data-service-value="' + row.service.id + '"  data-server-value="' + row.server.id + '"></i> ';
                            }

                            a += '<i class="fa action-button fa-database fa-action  fa-blue"  title="logs" data-action="log"  data-service-value="' + row.service.id + '"  data-server-value="' + row.server.id + '"></i>'; // row object contains the row data

                            return a;
                        }
                    }
                ],
                columnDefs: [{
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0,
                    checkboxes: {
                        selectRow: true
                    }
                }],
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
            });

            $(".title-selected").html(selectedText+" (<i class='fa action-button fa-refresh fa-action'" +
                " data-action='refresh'" +
                " data-server-value='"+selectedValue+"'" +
                "  style='color:blue;cursor:pointer;'></i>)");
            logUpdate();

        });



        function timediff(checked_at) {
            var current = new Date();
            var checked = checked_at;
            var seconds =  (current- checked)/1000;
            var resposne = "";
            if(seconds<60){
                resposne += Math.ceil(seconds)+"s ago";
            }else if (seconds<3600){
                resposne += Math.ceil(seconds/60)+"m ago";
            }else if (seconds<(3600*24)){
                resposne += Math.ceil(seconds/3600)+"h ago";
            }else{
                resposne += Math.ceil(seconds/(3600*24))+"d ago";
            }
            return resposne;
        }

        $('.service-select').on('change', function () {

            var selectedValue = $('select[name="service"]').val();
            var selectedText = $('select[name="service"] option:selected').text();
            $('.service-table').show();
            $('.service-table').html('');
            $('.data-tables').html('<table class="server-table"></table><table class="service-table">' +
                '<thead><tr><td></td><td>Service</td><td>Server</td><td>Started(last)</td><td>Stoped(last)</td><td>Status(updated at)</td><td>Action</td></tr></thead></table>');
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
                        data: 'service.name', name: 'service.name', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a = ''; // row object contains the row data
                            return a;
                        }
                    },
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
                        data: 'start', name: 'start', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var time = new Date(row.start*1000)
                            var name = (row.startby==null)?"":(row.startby.name);
                            var diff = name+"<br><span class='tinyinfo'>"+timediff(time)+"</span>";
                            return diff;
                        }
                    },
                    {
                        data: 'stop', name: 'stop', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var time = new Date(row.stop*1000)
                            var name = (row.stopby==null)?"":(row.stopby.name);
                            var diff = name+"<br><span class='tinyinfo'>"+timediff(time)+"</span>";
                            return diff;
                        }
                    },
                    {
                        data: 'status', name: 'status', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var checked_at = new Date(row.checked_at)
                            var diff = timediff(checked_at);
                            var a = '<span class="status-'+row.status+'"">'+row.status+'</span>'+
                            '<i class="fa action-button fa-refresh fa-action  fa-orange status-'+row.status+'" title="refresh status" data-action="refresh"  data-service-value="' + row.service.id + '"  data-server-value="' + row.server.id + '"></i> '
                                +"("+diff+")";
                            return a;
                        }
                    },
                    {
                        data: 'status', name: 'status', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                            var a="";
                            if(row.status!="RUNNING") {
                                a += '<i class="fa action-button fa-play fa-action fa-green" title="start" data-action="start" data-service-value="' + row.service.id + '"  data-server-value="' + row.server.id + '"></i> ';
                            }
                            if(row.status=="RUNNING") {
                                a += '<i class="fa action-button fa-stop fa-action fa-red"  title="stop" data-action="stop"  data-service-value="' + row.service.id + '"  data-server-value="' + row.server.id + '"></i> ';
                            }

                            a += '<i class="fa action-button fa-database fa-action  fa-blue"  title="logs" data-action="log"  data-service-value="' + row.service.id + '"  data-server-value="' + row.server.id + '"></i>'; // row object contains the row data

                            return a;
                        }
                    }
                ],
                columnDefs: [{
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0,
                    checkboxes: {
                        selectRow: true
                    }
                }],
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                }
            });

            $(".title-selected").text(selectedText);
            logUpdate();

        });

        function logUpdate() {
            var serverId = $('select[name="server"]').val();
            var serverValue = $('select[name="server"] option:selected').text();

            var serviceId = $('select[name="service"]').val();
            var serviceValue = $('select[name="service"] option:selected').text();

            serviceId = (serviceId=="Service")?null:serviceId;
            serverId = (serverId=="Server")?null:serverId;
            var url ="/action";

            $('.log-table').show();
            $('.log-table').html('');
            $('.data-tables-log').html('<table class="log-table"></table>');
            //$('.dataTables_wrapper').remove();

            if(logtable!=null)
                logtable.destroy();

            logtable = $('.log-table').DataTable({
                processing: true,
                bLengthChange: false,
                searching: false,
                serverSide: true,
                paging: false,
                ordering: false,
                ajax: "{{ url('/log-list') }}"+url+"?server="+serverId+"&service="+serviceId,
                columns: [
                    {data: 'message', name: 'message', searchable: false,
                        'render': function (data, type, row, meta) { // render event defines the markup of the cell text
                        var a= row.user.name+" - "+row.message;
                        a = a.replace("SUCCESSFULLY","<span style='color:green'>SUCCESSFULLY</span>");
                        a = a.replace(" start ","<b> start </b>");
                        a = a.replace(" stop ","<b> stop </b>");
                        a = a.replace("FAILED","<span style='color:red'>FAILED</span>");
                        a += " - <span class='meta-date' title='"+row.created_at+"'> "+timediff(new Date(row.created_at))+"</span>";
                        a = a.replace("--","<br><b>Log</b> - ");
                        return a;
                        }
                    },
                ]
            });
        }

        $(document).ready(function () {
            logUpdate();
        });

        $('body').on('click','.action-button',function(){
            var action = $(this).attr('data-action');
            var serverId = $(this).attr('data-server-value');
            var serviceId = $(this).attr('data-service-value');

            $(this).addClass("loader");

            console.log(action);
            if(action=="start" || action=="stop" || action=="log") {
                performAction(action, serverId, serviceId);
            }else if(action=="refresh"){
                var serverIp = "updated";
                performStatusUpdate(serverId,serverIp);
            }
        });

        function performStatusUpdate(server,serverIp) {
            $.ajax({
                type:'PUT',
                url:"{{ url('/servers/status/') }}/"+server,
                data:{server:server,"_token": "{{ csrf_token() }}"},
                success:function(data) {
                    $("#msg").html(data.message);
                    if(typeof servicetable != "undefined") {
                        servicetable.ajax.reload();
                    }else if(typeof servertable != "undefined"){
                        servertable.ajax.reload();
                    }

                    if(data.hasOwnProperty('success')) {
                        var title = serverIp+' status Successfuly';
                        var message = data.success;
                        success(title,message);
                    }else{
                        var title = serverIp+' status Failed';
                        var message = data.error;
                        error(title,message);
                    }
                    $('.action-button').removeClass("loader");
                    logUpdate();

                },
                error: function(xhr, msg){
                    var title = serverIp+' status Failed';
                    error(title,msg);
                    $('.action-button').removeClass("loader");
                    logUpdate();
                }
            });
        }

        function performAction(action,server,service) {
            $.ajax({
                type:'POST',
                url:"{{ url('/services/') }}/perform",
                data:{server:server,service:service,action:action,"_token": "{{ csrf_token() }}"},
                success:function(data) {
                    $("#msg").html(data.message);

                    if(data.hasOwnProperty('success')) {
                        var title = action+' Successfuly';
                        var message = data.success;
                        success(title,message);

                        if(action=="log"){
                            $(".log-body").html("");
                            var messageArr = data.message.split("\n");
                            $(messageArr).each(function(k,v){
                                $(".log-body").append("<li>"+v+"</li>");
                            });
                            $('#logModal').modal('show');
                        }else{
                            performStatusUpdate(server,"updated");
                        }
                    }else{
                        var title = action+' Failed';
                        var message = data.error;
                        error(title,message);
                        performStatusUpdate(server,"updated");
                    }
                    $('.action-button').removeClass("loader");
                    logUpdate();

                },
                error: function(xhr, msg){
                    var title = action+' Failed';
                    error(title,msg);
                    $('.action-button').removeClass("loader");
                    logUpdate();
                }
            });
        }

    </script>
@stop

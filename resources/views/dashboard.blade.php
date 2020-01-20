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
                    <li class="text-right"><i class="ti-arrow-up text-success"></i> <span class="counter text-success">@yield('server-count')</span></li>
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
                    <li class="text-right"><i class="ti-arrow-up text-purple"></i> <span class="counter text-purple">@yield('service-count')</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 col-xs-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">Total Instances</h3>
                <ul class="list-inline two-part">
                    <li>
                        <div id="sparklinedash3"></div>
                    </li>
                    <li class="text-right"><i class="ti-arrow-up text-info"></i> <span class="counter text-info">@yield('instance-count')</span></li>
                </ul>
            </div>
        </div>
    </div>
    <!--/.row -->
    <!-- ============================================================== -->
    <div class="row">
        <!-- .col -->
        <div class="col-md-12 col-lg-8 col-sm-12">
            <div class="white-box">
                <div class="col-md-4 col-sm-4 col-xs-6 pull-right">
                    {{ Form::select ('service', $serverList, null , ['class' =>'form-control pull-right row b-none']) }}
                </div>
                <div class="col-md-1 col-sm-2 col-xs-2 pull-right">
                    <span style="padding: 10px;display: block;">OR</span>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-6 pull-right">
                    {{ Form::select ('service', $serviceList, null , ['class' =>'form-control pull-right row b-none']) }}
                </div>
                <h3 class="box-title">Live Status</h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>SERVER</th>
                            <th>SERVICE</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ Form::checkbox('instance',1,null) }}</td>
                            <td class="txt-oflo">192.168.1.1</td>
                            <td>Duplicate Serviec</td>
                            <td class="txt-oflo"><span class="text-success">Running</span></td>
                            <td><i class="fa fa-play fa-action fa-green"></i> <i class="fa fa-stop fa-action fa-red"></i> <i class="fa fa-refresh fa-action  fa-orange"></i> <i class="fa fa-database fa-action  fa-blue"></i></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                Bulk Action : <i class="fa fa-play fa-action"></i> <i class="fa fa-stop fa-action"></i> <i class="fa fa-refresh fa-action"></i>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="panel">
                <div class="sk-chat-widgets">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Recent Activity
                        </div>
                        <div class="panel-body">
                            <ul class="chatonline">
                                <li>
                                    <a href="javascript:void(0)"> <span>Akash Kumar <small class="text-success">Started - vendor service on 192.168.10.19, 192.168.10.19, 192.168.10.19, 192.168.10.19</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"> <span>Varun Goel <small class="text-warning">Stopped - vendor service on 192.168.10.19, 192.168.10.19, 192.168.10.19, 192.168.10.19</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"> <span>Varun <small class="text-muted">RESTARTED - vendor service on 192.168.10.19, 192.168.10.19, 192.168.10.19, 192.168.10.19</small></span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>
@stop

@extends('layouts.user', [
        'title' => 'TEST!',
        'breadcrumbs' => [
            ['Name', route('monitoring.meters')],
            ['ololo', route('tasks.list')],
            ['tasks', route('tasks.list')],
            ['test'],
        ]
    ])

@section('content')
    <!-- Main content-->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-md-9 col-sm-8 col-xs-12">
                            <div class="panel main-metric no-margin-top">
                                <div class="metric-left">
                                    <div class="metric-panel text-left">
                                        <div class="metric-text">
                                            <h4> Visitors this month: </h4>
                                            <h2> 59 734 </h2>
                                        </div>

                                        <div class="progress progress-sm">
                                            <div style="width: 80%" class="progress-bar progress-bar-success"></div>
                                        </div>
                                        <h6> <i class="fa fa-caret-up text-success"></i>  80% higher than previous month</h6>
                                    </div>
                                </div>

                                <div class="metric-right">
                                    <i class="fa fa-eye text-success"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-4 col-xs-12">
                            <div class="panel metric no-margin-top">
                                <div class="metric-panel no-padding-top" style="min-height: 94px;">
                                    <div class="metrics-text row">
                                        <h5 class="col-xs-12 no-margin-top">
                                            <b class="pull-left">Server 1:</b>
                                            <span class="label label-success pull-right"> 35% Full </span>
                                        </h5>
                                        <h5 class="col-xs-12">
                                            <b class="pull-left">Server 2:</b>
                                            <span class="label label-warning pull-right"> 40% Full </span>
                                        </h5>
                                        <h5 class="col-xs-12 no-margin-bottom">
                                            <b class="pull-left">Server 3:</b>
                                            <span class="label label-danger pull-right"> 25% Full </span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="clear"></div>
                                <div class="metric-footer">
                                    <div class="progress progress-sm progress-striped no-margin active">
                                        <div style="width: 35%" class="progress-bar progress-bar-success">
                                        </div>
                                        <div style="width: 40%" class="progress-bar progress-bar-warning">
                                        </div>
                                        <div style="width: 25%" class="progress-bar progress-bar-danger">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <div class="panel">
                                <div class="panel-heading">
                                    Sales this month
                                </div>
                                <div class="panel-body">
                                    <div id="area-chart" style="height: 200px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="panel panel-info-outline tabs-panel">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#dashboard-personal" aria-expanded="true"> Personal </a></li>
                                <li class=""><a data-toggle="tab" href="#dashboard-feed" aria-expanded="false"> Feed </a></li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <ul class="list-unstyled notifications notifications-panel no-border no-margin-bottom tab-pane active" id="dashboard-personal">
                                <li>
                                    <img alt="" class="img-circle small-image vcentered" src="images/profile2.png">
                                    <div class="notification-title vcentered info-combo">
                                        <h4 class="no-margin-top"> Elon Musk </h4>
                                        <h6 class="no-margin text-muted"> Entrepreneur </h6>
                                    </div>
                                    <span class="notification-time text-success">  active </span>
                                </li>
                                <li>
                                    <img alt="" class="img-circle small-image vcentered info-combo" src="images/profile3.png">
                                    <div class="notification-title vcentered info-combo">
                                        <h4 class="no-margin-top"> Jonathan Ive </h4>
                                        <h6 class="no-margin text-muted"> Chief Designer </h6>
                                    </div>
                                    <span class="notification-time text-muted">  offline </span>
                                </li>
                                <li>
                                    <img alt="" class="img-circle small-image vcentered" src="images/profile9.png">
                                    <div class="notification-title vcentered info-combo">
                                        <h4 class="no-margin-top"> Pavel Durov </h4>
                                        <h6 class="no-margin text-muted"> Entrepreneur </h6>
                                    </div>
                                    <span class="notification-time text-warning">  away </span>
                                </li>
                                <li>
                                    <img alt="" class="img-circle small-image vcentered" src="images/profile8.png">
                                    <div class="notification-title vcentered info-combo">
                                        <h4 class="no-margin-top">Jeff Williams </h4>
                                        <h6 class="no-margin text-muted"> Chief Opperating Officer </h6>
                                    </div>
                                    <span class="notification-time text-warning">  away </span>
                                </li>
                                <li>
                                    <img alt="" class="img-circle small-image vcentered" src="images/profile5.png">
                                    <div class="notification-title vcentered info-combo">
                                        <h4 class="no-margin-top"> Mark Zuckerberg </h4>
                                        <h6 class="no-margin text-muted"> Senior Developer </h6>
                                    </div>
                                    <span class="notification-time text-success">  active </span>
                                </li>
                                <li>
                                    <img alt="" class="img-circle small-image vcentered" src="images/profile4.png">
                                    <div class="notification-title vcentered info-combo">
                                        <h4 class="no-margin-top"> Evan Williams </h4>
                                        <h6 class="no-margin text-muted"> Senior Developer </h6>
                                    </div>
                                    <span class="notification-time text-muted">  offline </span>
                                </li>
                            </ul>
                            <ul class="list-unstyled notifications notifications-panel no-border no-margin-bottom tab-pane" id="dashboard-feed">
                                <li>
                                    <i class="fa fa-upload small-icon text-center vcentered"></i>
                                    <div class="notification-title vcentered">
                                        <b> Server 1 </b> has been successfully updated.
                                    </div>
                                    <span class="notification-time text-muted">  6m ago </span>
                                </li>
                                <li>
                                    <img alt="" class="img-circle small-image vcentered" src="images/profile5.png">
                                    <div class="notification-title vcentered">
                                        <b> Mark Zuckerberg </b> has fixed <b> 3 </b> important bugs.
                                    </div>
                                    <span class="notification-time text-muted">  12m ago </span>
                                </li>
                                <li>
                                    <i class="fa fa-refresh small-icon text-center vcentered"></i>
                                    <div class="notification-title vcentered">
                                        <b> Server 2 </b> has been successfully restarted.
                                    </div>
                                    <span class="notification-time text-muted">  25m ago </span>
                                </li>
                                <li>
                                    <i class="fa fa-desktop small-icon text-center text-warning vcentered"></i>
                                    <div class="notification-title vcentered">
                                        <b class="text-warning"> Server 4 </b> was suspended.
                                    </div>
                                    <span class="notification-time text-muted">  2h ago </span>
                                </li>
                                <li>
                                    <img alt="" class="img-circle small-image vcentered" src="images/profile4.png">
                                    <div class="notification-title vcentered">
                                        <b> Evan Williams </b> has become a <b> senior developer </b>.
                                    </div>
                                    <span class="notification-time text-muted">  3h ago </span>
                                </li>
                                <li>
                                    <i class="fa fa-user small-icon text-center text-success vcentered"></i>
                                    <div class="notification-title vcentered">
                                        <b class="text-success"> 398 </b> new users have registred.
                                    </div>
                                    <span class="notification-time text-muted">  5h ago </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-4 col-xs-6">
                            <div class="weather-metric info">
                                <div class="metric-left">
                                    <div class="metric-panel text-left">
                                        <div class="metric-text info-combo">
                                            <h2> Tokyo </h2>
                                            <h4> Japan </h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="metric-right">
                                    <i class="ion ion-ios-snowy vcentered"></i>
                                    <h3 class="weather-metric-temperature vcentered"> -2 <span class="degrees">&deg;C</span></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-xs-6">
                            <div class="weather-metric success">
                                <div class="metric-left">
                                    <div class="metric-panel text-left">
                                        <div class="metric-text info-combo">
                                            <h2> London </h2>
                                            <h4> Great Britain </h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="metric-right">
                                    <i class="fa fa-leaf vcentered"></i>
                                    <h3 class="weather-metric-temperature vcentered"> 14 <span class="degrees">&deg;C</span></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-xs-6">
                            <div class="weather-metric warning">
                                <div class="metric-left">
                                    <div class="metric-panel text-left">
                                        <div class="metric-text info-combo">
                                            <h2> Seattle </h2>
                                            <h4> U.S.A </h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="metric-right">
                                    <i class="fa fa-sun-o vcentered"></i>
                                    <h3 class="weather-metric-temperature vcentered"> 25 <span class="degrees">&deg;C</span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="panel">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Team</th>
                                            <th>Members</th>
                                            <th>Office</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>UX Development</td>
                                            <td>33</td>
                                            <td>Tokyo</td>
                                            <td>
                                                <h6 class="no-margin text-success">done</h6>
                                                <div class="progress progress-sm progress-striped no-margin">
                                                    <div style="width: 100%" class="progress-bar progress-bar-success">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>UI Development</td>
                                            <td>47</td>
                                            <td>London</td>
                                            <td>
                                                <h6 class="no-margin text-success">done</h6>
                                                <div class="progress progress-sm progress-striped no-margin">
                                                    <div style="width: 100%" class="progress-bar progress-bar-success">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Frond End</td>
                                            <td>66</td>
                                            <td>Seattle</td>
                                            <td>
                                                <h6 class="no-margin text-muted">46%</h6>
                                                <div class="progress progress-sm progress-striped no-margin">
                                                    <div style="width: 46%" class="progress-bar progress-bar-warning">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Back End</td>
                                            <td>41</td>
                                            <td>London</td>
                                            <td>
                                                <h6 class="no-margin text-muted">86%</h6>
                                                <div class="progress progress-sm progress-striped active no-margin">
                                                    <div style="width: 86%" class="progress-bar progress-bar-info">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Testers</td>
                                            <td>16</td>
                                            <td>London</td>
                                            <td>
                                                <h6 class="no-margin text-muted">unvailable</h6>
                                                <div class="progress progress-sm  no-margin">
                                                    <div style="width: 0" class="progress-bar progress-bar-success">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Tasks
                        </div>
                        <div class="panel-body">
                            <ul>
                                <li>
                                    <h4>
                                        Calendar plugin
                                        <span class="label label-success"> done </span>
                                    </h4>
                                    <ul>
                                        <li> Update design </li>
                                        <li> Improve code </li>
                                    </ul>
                                </li>
                                <li>
                                    <h4>
                                        Images upload plugin
                                        <span class="label label-success"> done </span>
                                    </h4>
                                </li>
                                <li>
                                    <h4>
                                        Find 2 more UI designers
                                        <span class="label label-muted"> default </span>
                                    </h4>
                                </li>
                                <li>
                                    <h4>
                                        Sessions algorithm
                                        <span class="label label-warning"> important </span>
                                    </h4>
                                </li>
                                <li>
                                    <h4>
                                        Update graphs
                                        <span class="label label-warning"> important </span>
                                    </h4>
                                </li>
                                <li>
                                    <h4>
                                        Update labels
                                        <span class="label label-success"> done </span>
                                    </h4>
                                </li>
                                <li>
                                    <h4>
                                        Test calendar plugin
                                        <span class="label label-success"> done </span>
                                    </h4>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Main content-->
@endsection
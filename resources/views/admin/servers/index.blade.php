@extends('layouts.admin')

@section('title')
    List Servers
@endsection

@section('content-header')
    <h1>Servers<small>All servers available on the system.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Servers</li>
    </ol>
@endsection

@section('content')

{{-- Privacy mode access restriction notice --}}
@if(session()->has('pterodactyl::flashes.info'))
    <div class="row">
        <div class="col-xs-12">
            <div class="callout callout-info" style="border-left-color: #00c0ef;">
                <h4><i class="fa fa-lock"></i> Access Restricted</h4>
                @foreach(session('pterodactyl::flashes.info') as $message)
                    <p>{!! $message !!}</p>
                @endforeach
                <p class="text-muted" style="margin-bottom:0;font-size:12px;">
                    This server's owner has enabled privacy mode. Only the primary administrator can access its configuration panel.
                </p>
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Server List</h3>
                <div class="box-tools search01">
                    <form action="{{ route('admin.servers') }}" method="GET">
                        <div class="input-group input-group-sm">
                            <input type="text" name="filter[*]" class="form-control pull-right" value="{{ request()->input()['filter']['*'] ?? '' }}" placeholder="Search Servers">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                <a href="{{ route('admin.servers.new') }}"><button type="button" class="btn btn-sm btn-primary" style="border-radius: 0 3px 3px 0;margin-left:-1px;">Create New</button></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>Server Name</th>
                            <th>UUID</th>
                            <th>Owner</th>
                            <th>Node</th>
                            <th>Connection</th>
                            <th></th>
                            <th></th>
                        </tr>
                        @foreach ($servers as $server)
                            <tr data-server="{{ $server->uuidShort }}">
                                <td>
                                    <a href="{{ route('admin.servers.view', $server->id) }}">{{ $server->name }}</a>
                                    @if($server->hide_from_admin)
                                        <span
                                            class="label label-default"
                                            style="margin-left:5px;font-size:10px;"
                                            data-toggle="tooltip"
                                            data-placement="right"
                                            title="Owner has enabled privacy mode — only the primary admin can access this server's configuration."
                                        >
                                            <i class="fa fa-lock"></i> Private
                                        </span>
                                    @endif
                                </td>
                                <td><code title="{{ $server->uuid }}">{{ $server->uuid }}</code></td>
                                <td><a href="{{ route('admin.users.view', $server->user->id) }}">{{ $server->user->username }}</a></td>
                                <td><a href="{{ route('admin.nodes.view', $server->node->id) }}">{{ $server->node->name }}</a></td>
                                <td>
                                    <code>{{ $server->allocation->alias }}:{{ $server->allocation->port }}</code>
                                </td>
                                <td class="text-center">
                                    @if($server->isSuspended())
                                        <span class="label bg-maroon">Suspended</span>
                                    @elseif(! $server->isInstalled())
                                        <span class="label label-warning">Installing</span>
                                    @else
                                        <span class="label label-success">Active</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-xs btn-default" href="/server/{{ $server->uuidShort }}"><i class="fa fa-wrench"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($servers->hasPages())
                <div class="box-footer with-border">
                    <div class="col-md-12 text-center">{!! $servers->appends(['filter' => Request::input('filter')])->render() !!}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    <script>
        $('.console-popout').on('click', function (event) {
            event.preventDefault();
            window.open($(this).attr('href'), 'Pterodactyl Console', 'width=800,height=400');
        });

        // Init tooltips untuk badge privacy
        $('[data-toggle="tooltip"]').tooltip();
    </script>
@endsection

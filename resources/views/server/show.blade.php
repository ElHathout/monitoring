@extends('layouts.app')

@section('content')
<script>
window.monitorURL = "{{ url('/') }}";
window.monitorServerID = {{ $server->id }};
window.monitorServerToken = "{{ $server->read_token }}";
</script>
<script src="/js/sensors.js"></script>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <p>{!! $server->status()->badge() !!}</p>

                    <p>
                        Last heartbeet:<br>
                        {{ $server->info()->lastRecordTime()->toDateTimeString() }}<br>
                        ({{ $server->info()->lastRecordTime()->diffForHumans() }})
                    </p>

                    <p>Client version: {{ $server->info()->clientVersion() }}</p>

                    <p>Uptime: {{ $server->info()->uptime() }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <p>{{ $server->info()->manufacturer() }} {{ $server->info()->productName() }}</p>
                    <p><small>{{ $server->info()->uuid() }}</small></p>
                    <p>{{ $server->info()->cpuinfo()["cpu"] }}
                        ({{ $server->info()->cpuinfo()["threads"] }} threads)</p>
                    <p>Memory: {{ $server->info()->meminfo() }}</p>
                    <p>{{ $server->info()->lsb() }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Danger zone
                </div>

                <div class="card-body">
                    <p>Server id: <code>{{ $server->id }}</code></p>
                    <p>Token: <code>{{ $server->token }}</code></p>

                    <div>
                        <a class="btn btn-primary btn-sm"
                           href="{{ action('ServerController@edit', ['server' => $server]) }}">
                            <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                        </a>

                        <form method="POST"
                              action="{{ action('ServerController@destroy', ['server' => $server]) }}"
                              style="display: inline-block">
                            {{ csrf_field() }}
                            {{ method_field("DELETE") }}
                            <button class="btn btn-danger btn-sm">
                                <i class="fa fa-times-circle" aria-hidden="true"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h1>
                <a href="{{ $server->organization->url() }}">{{ $server->organization->name }}</a>
                / {{ $server->name }}
            </h1>

            @if ($server->hasData())
            @foreach ($server->getSensors() as $sensor)
            <div class="card">
                <div class="card-header">
                    {{ $sensor->name() }}

                    <div class="float-right">
                        {!! $sensor->status()->badge() !!}
                    </div>
                </div>
                <div class="card-body">
                    {!! $sensor->report() !!}
                </div>
            </div>
            @endforeach

            <div class="card">
                <div class="card-header">
                    History
                </div>
                <div class="card-body">
                    <table class='table table-sm'>
                        @foreach($server->getChanges() as $change)
                        <tr>
                            <td>{{ $change->getTimeCarbon()->toDateTimeString() }}</td>
                            <td>{!! $change->status()->badge() !!}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            @endif

            <div class="card">
                <div class="card-header">
                    PHP Client installation
                </div>
                <div class="card-body">
                    <p>Download client application:</p>
                    <pre style="font-size: 75%; overflow: hidden"><code>wget {{ $server->info()->lastClientUrl() }}
unzip monitor-*.zip
</code></pre>
                    <p>Move it to /usr/bin:</p>
                    <pre style="font-size: 75%; overflow: hidden"><code>sudo mv monitor /usr/bin/monitor
</code></pre>

                    <p>Test it:</p>
                    <pre style="font-size: 75%; overflow: hidden"><code>sudo monitor ping -i {{ $server->id }} -t {{ $server->token }}
</code></pre>

                    <p>Add a cron entry to run it automatically:</p>
                    <pre style="font-size: 75%; overflow: hidden"><code>echo "*/5 * * * * root /usr/bin/monitor ping -i {{ $server->id }} -t {{ $server->token }}" | \
sudo tee -a /etc/crontab
            </code></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

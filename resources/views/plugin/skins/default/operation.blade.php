    <div class="panel">
        <div class="panel-body">
            <strong>최근작업</strong>

            @if($operation['status'] !== 'running')
                <div class="pull-right">
                    <form action="{{ route('settings.plugins.operation.delete') }}" method="DELETE" data-submit="xe-ajax" data-callback="deletePluginOperation">
                        <button class="btn-link" type="submit">삭제</button>
                    </form>
                </div>
            @endif
            <hr>
            <label for="">작업</label>
            @foreach($operation['runnings'] as $package => $version)
                <p>{{ data_get($operation['runningsInfo'], $package.'.title') }}({{ data_get($operation['runningsInfo'], $package.'.pluginId') }}) ver.{{ $version }} {{ array_get(['install'=>'설치','update'=>'업데이트','uninstall'=>'삭제'], $operation['runningMode']) }}</p>
            @endforeach

            <label for="">상태</label>
            <p>
                @if($operation['status'] === 'successed')
                    성공
                @elseif($operation['status'] === 'failed')
                    실패
                @elseif($operation['status'] === 'expired')
                    실패(제한시간 초과)
                @else
                    진행중
                @endif
            </p>

            @if($operation['status'] === 'successed')
                <label for="">변경내역</label>
                @foreach($operation['changed'] as $mode => $plugins)
                    @foreach($plugins as $plugin => $version)
                    <p>{{ $plugin }} ver.{{ $version }} {{ $mode }}</p>
                    @endforeach
                @endforeach
            @endif
        </div>
    </div>

@extends('admin.master')
@section('content')
@section('title')
    School MIS Integration
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-school fa-fw"></i> StawiSMS (School MIS) Integration</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                @foreach($errors->all() as $error)
                                    <strong>{!! $error !!}</strong><br>
                                @endforeach
                            </div>
                        @endif

                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif

                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        @if(!empty($plainPullToken))
                            <div class="alert alert-warning">
                                <strong>Copy this API key now</strong> — it will not be shown again.<br>
                                Paste it into <strong>StawiSMS → Settings → Integrations → HR → Remote system token</strong>.
                                <div class="input-group" style="margin-top:10px;">
                                    <input type="text" class="form-control" id="plain_pull_token" value="{{ $plainPullToken }}" readonly>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" onclick="navigator.clipboard.writeText(document.getElementById('plain_pull_token').value); this.innerText='Copied';">Copy</button>
                                    </span>
                                </div>
                            </div>
                        @endif

                        <p class="text-muted">
                            Turn this on to exchange employees and leave with StawiSMS.
                            Two keys are required:
                        </p>
                        <ol class="text-muted">
                            <li><strong>HR API key</strong> (generated below) → paste into StawiSMS as the remote system token (school pulls from HR).</li>
                            <li><strong>School API key</strong> (from StawiSMS Integrations) → paste below so HR can push into the school.</li>
                        </ol>

                        <form method="POST" action="{{ route('schoolMisSettings.update') }}" class="form-horizontal">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="panel panel-default">
                                        <div class="panel-heading"><i class="fa fa-power-off"></i> Integration status</div>
                                        <div class="panel-body text-center">
                                            <div class="form-group">
                                                <label class="control-label">Enabled</label>
                                                <div>
                                                    <input type="checkbox" name="is_enabled" id="is_enabled" value="1" switch="none" {{ $settings->is_enabled ? 'checked' : '' }}>
                                                    <label for="is_enabled" data-on-label="On" data-off-label="Off"></label>
                                                </div>
                                                <p class="help-block" style="margin-top:10px;">
                                                    When turned on for the first time, an API key is generated automatically for StawiSMS.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="panel panel-default">
                                        <div class="panel-heading"><i class="fa fa-user"></i> Push employees</div>
                                        <div class="panel-body text-center">
                                            <div class="form-group">
                                                <label class="control-label">On employee save</label>
                                                <div>
                                                    <input type="checkbox" name="push_on_employee_save" id="push_on_employee_save" value="1" switch="none" {{ $settings->push_on_employee_save ? 'checked' : '' }}>
                                                    <label for="push_on_employee_save" data-on-label="On" data-off-label="Off"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="panel panel-default">
                                        <div class="panel-heading"><i class="fa fa-calendar"></i> Push leave</div>
                                        <div class="panel-body text-center">
                                            <div class="form-group">
                                                <label class="control-label">On leave approve/change</label>
                                                <div>
                                                    <input type="checkbox" name="push_on_leave_approve" id="push_on_leave_approve" value="1" switch="none" {{ $settings->push_on_leave_approve ? 'checked' : '' }}>
                                                    <label for="push_on_leave_approve" data-on-label="On" data-off-label="Off"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top:15px;">
                                <div class="col-md-4">
                                    <div class="panel panel-default">
                                        <div class="panel-heading"><i class="fa fa-car"></i> Sync vehicles</div>
                                        <div class="panel-body text-center">
                                            <div class="form-group">
                                                <label class="control-label">Enable vehicle sync</label>
                                                <div>
                                                    <input type="checkbox" name="sync_vehicles" id="sync_vehicles" value="1" switch="none" {{ ($settings->sync_vehicles ?? true) ? 'checked' : '' }}>
                                                    <label for="sync_vehicles" data-on-label="On" data-off-label="Off"></label>
                                                </div>
                                                <p class="help-block">Keeps fleet + driver assignments aligned with StawiSMS Transport.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="panel panel-default">
                                        <div class="panel-heading"><i class="fa fa-exchange"></i> Push vehicles</div>
                                        <div class="panel-body text-center">
                                            <div class="form-group">
                                                <label class="control-label">On vehicle/assignment change</label>
                                                <div>
                                                    <input type="checkbox" name="push_on_vehicle_change" id="push_on_vehicle_change" value="1" switch="none" {{ ($settings->push_on_vehicle_change ?? true) ? 'checked' : '' }}>
                                                    <label for="push_on_vehicle_change" data-on-label="On" data-off-label="Off"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h4>School (StawiSMS) connection — for HR → School push</h4>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="school_base_url">School base URL</label>
                                <div class="col-md-7">
                                    <input type="url" class="form-control" name="school_base_url" id="school_base_url"
                                           value="{{ old('school_base_url', $settings->school_base_url) }}"
                                           placeholder="http://localhost:8000">
                                    <span class="help-block">StawiSMS app URL (no trailing <code>/api</code> required).</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="school_api_key">School API key</label>
                                <div class="col-md-7">
                                    <input type="password" class="form-control" name="school_api_key" id="school_api_key"
                                           value="{{ $settings->school_api_key ? '••••••••••••••••' : '' }}"
                                           autocomplete="new-password"
                                           placeholder="Paste inbound key from StawiSMS Integrations">
                                    <span class="help-block">Generated in StawiSMS → Integrations → Generate API key. Leave masked value unchanged to keep the current key.</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="timeout">Request timeout (seconds)</label>
                                <div class="col-md-3">
                                    <input type="number" min="5" max="120" class="form-control" name="timeout" id="timeout"
                                           value="{{ old('timeout', $settings->timeout ?? 30) }}">
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> Save settings</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading"><i class="fa fa-key"></i> HR API key (for StawiSMS pull)</div>
                                    <div class="panel-body">
                                        <p class="text-muted">
                                            StawiSMS stores this as the <strong>remote system token</strong> and sends it when pulling
                                            <code>/api/internal/sync/*</code>.
                                        </p>
                                        @if($settings->hasPullApiToken())
                                            <p><code>{{ $settings->pull_api_token_hint }}</code></p>
                                            <p class="text-muted">
                                                Generated {{ optional($settings->pull_api_token_generated_at)->format('d M Y H:i') }}
                                            </p>
                                        @elseif($settings->pull_api_token_revoked_at)
                                            <div class="alert alert-danger">Key revoked on {{ $settings->pull_api_token_revoked_at->format('d M Y H:i') }}.</div>
                                        @else
                                            <div class="alert alert-secondary">No API key yet. Enable the integration or generate a key.</div>
                                        @endif

                                        <form method="POST" action="{{ route('schoolMisSettings.generateKey') }}" style="display:inline-block;"
                                              onsubmit="return confirm('Generate a new API key? The previous key will stop working immediately.');">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                {{ $settings->hasPullApiToken() ? 'Rotate / regenerate key' : 'Generate API key' }}
                                            </button>
                                        </form>
                                        @if($settings->hasPullApiToken())
                                            <form method="POST" action="{{ route('schoolMisSettings.revokeKey') }}" style="display:inline-block;margin-left:6px;"
                                                  onsubmit="return confirm('Revoke this API key? School pull will stop until a new key is generated.');">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">Revoke key</button>
                                            </form>
                                        @endif

                                        <hr>
                                        <p class="text-muted small mb-0">
                                            Sync base: <code>{{ $internalSyncBase }}</code><br>
                                            Endpoints: <code>/employees</code>, <code>/leave</code>, <code>/leave/on-leave</code>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading"><i class="fa fa-exchange"></i> Actions</div>
                                    <div class="panel-body">
                                        <form method="POST" action="{{ route('schoolMisSettings.testConnection') }}" style="margin-bottom:10px;">
                                            @csrf
                                            <button type="submit" class="btn btn-default btn-block"
                                                {{ $settings->pushConfigured() ? '' : 'disabled' }}>
                                                <i class="fa fa-plug"></i> Test school connection
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('schoolMisSettings.pushNow') }}">
                                            @csrf
                                            <button type="submit" class="btn btn-info btn-block"
                                                {{ $settings->pushConfigured() ? '' : 'disabled' }}
                                                onclick="return confirm('Push all employees and approved leave to StawiSMS now?');">
                                                <i class="fa fa-cloud-upload"></i> Push employees &amp; leave now
                                            </button>
                                        </form>
                                        @unless($settings->pushConfigured())
                                            <p class="text-warning" style="margin-top:12px;">
                                                Enable the integration and set School base URL + School API key to use these actions.
                                            </p>
                                        @endunless
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

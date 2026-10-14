@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.newrmacode.title'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('strings.backend.newrmacode.title')</h4>
                </div><!--card-header-->
<form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.rmacode.store') }}">
                <div class="card-body">
                      {{ csrf_field() }}
                      <div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
                          <label for="code" class="col-md-2 form-control-label">RMACode <span class="required">*</span></label>
                          <div class="col-md-10">
                              <input id="name" type="text" class="form-control col-md-7 col-xs-12" name="code" value="{{ old('code') }}" required autofocus>
                              @if ($errors->has('code'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('problem') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div>

                    </div><!--card-body-->
                    <div class="card-footer clearfix">
                                    <div class="row">
                                        <div class="col">
                                            <a class="btn btn-danger btn-sm" href="{{ route('admin.rmacode.index') }}">Cancel</a>
                                        </div><!--col-->

                                        <div class="col text-right">
                                            <button class="btn btn-success btn-sm pull-right" type="submit">Create</button>
                                        </div><!--col-->
                                    </div><!--row-->
                                </div><!--card-footer-->

                  </form>

                </div>
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->
@endsection

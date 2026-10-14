@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.newcustomer.title'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('strings.backend.newcustomer.title')</h4>
                </div><!--card-header-->
<form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.customer.store') }}">
                <div class="card-body">

                      {{ csrf_field() }}
                      <div class="form-group row{{ $errors->has('first_name') ? ' has-error' : '' }}">
                          <label for="first_name" class="col-md-2 form-control-label">First Name <span class="required">*</span></label>
                          <div class="col-md-10">
                              <input id="name" type="text" class="form-control col-md-7 col-xs-12" name="first_name" value="{{ old('first_name') }}" required autofocus>
                              @if ($errors->has('first_name'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('first_name') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div>
                      <div class="form-group row{{ $errors->has('last_name') ? ' has-error' : '' }}">
                          <label for="last_name" class="control-label col-md-2">Last Name <span class="required">*</span></label>
                          <div class="col-md-10">
                              <input id="username" type="text" class="form-control col-md-7 col-xs-12" name="last_name" value="{{ old('last_name') }}" required>
                              @if ($errors->has('last_name'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('last_name') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div>

                      <div class="form-group row{{ $errors->has('status') ? ' has-error' : '' }}">
                          <label for="status" class="col-md-2 control-label">Status</label>
                          <div class="col-md-10">
                              <select id="status" type="text" class="form-control col-md-7 col-xs-12" name="status" value="{{ old('status') }}" required>
                                <option @if (old('permission')=='1')
                                          selected
                                        @endif
                                 value="1" >Active</option>
                                <option
                                <option @if (old('permission')=='0')
                                          selected
                                        @endif
                                value="0">Inactive</option>

                              </select>
                              @if ($errors->has('status'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('status') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div>

                      <div class="form-group row{{ $errors->has('emails') ? ' has-error' : '' }}">
                          <label for="emails" class="col-md-2 control-label">E-Mail <span class="Required">*</span></label>
                          <div class="col-md-10">
                              <input id="email" type="email" class="form-control col-md-7 col-xs-12" name="emails" value="{{ old('emails') }}" required>
                              @if ($errors->has('emails'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('emails') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div>
                      <div class="form-group row{{ $errors->has('phone') ? ' has-error' : '' }}">
                          <label for="phone" class="col-md-2 control-label">Phone</label>
                          <div class="col-md-10">
                              <input id="phone" type="text" class="form-control col-md-7 col-xs-12" name="phone">
                              @if ($errors->has('phone'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('phone') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div>
                      <div class="form-group row">
                          <label for="address" class="col-md-2 control-label">Address</label>
                          <div class="col-md-10">
                              <input id="address" type="text" class="form-control col-md-7 col-xs-12" name="address">
                              @if ($errors->has('address'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('address') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div>
                    </div><!--card-body-->
                    <div class="card-footer clearfix">
                                    <div class="row">
                                        <div class="col">
                                            <a class="btn btn-danger btn-sm" href="{{ route('admin.customer.index') }}">Cancel</a>
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

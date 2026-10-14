@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.editbrand.title'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('strings.backend.editbrand.title')</h4>
                </div><!--card-header-->
<form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.brand.update',['id'=>$brand->id]) }}">
                <div class="card-body">

                      {{ csrf_field() }}
                      <input type="hidden" name="_method" value="PATCH">
                      <div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
                          <label for="name" class="col-md-2 form-control-label">Brand <span class="required">*</span></label>
                          <div class="col-md-10">
                              <input id="name" type="text" class="form-control col-md-7 col-xs-12" name="name" value="{{ old('name',$brand->name) }}" required autofocus>
                              @if ($errors->has('name'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('problem') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div>
                      <div class="form-group row{{ $errors->has('description') ? ' has-error' : '' }}">
                          <label for="description" class="control-label col-md-2">Description <span class="required">*</span></label>
                          <div class="col-md-10">
                              <input id="username" type="text" class="form-control col-md-7 col-xs-12" name="description" value="{{ old('description',$brand->description) }}" required>
                              @if ($errors->has('description'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('description') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div>

                    </div><!--card-body-->
                    <div class="card-footer clearfix">
                                    <div class="row">
                                        <div class="col">
                                            <a class="btn btn-danger btn-sm" href="{{ route('admin.brand.index') }}">Cancel</a>
                                        </div><!--col-->

                                        <div class="col text-right">
                                            <button class="btn btn-success btn-sm pull-right" type="submit">Update</button>
                                        </div><!--col-->
                                    </div><!--row-->
                                </div><!--card-footer-->

                  </form>

                </div>
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->
@endsection

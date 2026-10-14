@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.newproblem.title'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('strings.backend.newproblem.title')</h4>
                </div><!--card-header-->
<form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.problem.store') }}">
                <div class="card-body">

                      {{ csrf_field() }}
                      <div class="form-group row{{ $errors->has('groupproblem') ? ' has-error' : '' }}">
                      <label for="groupproblem" class="col-md-2 form-control-label">Group Name<span class="required">*</span></label>
                      <div class="col-md-4">
                        {{ html()->select('groupproblem')
                                 ->class('form-control')
                                 ->options($groupproblems)
                                 ->value('1')
                                 ->placeholder('Select Group')
                                 ->required()

                              }}
                      </div>
                    </div>

                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                          <label for="problem" class="col-md-2 form-control-label">Problem <span class="required">*</span></label>

                          <div class="col-md-10">
                              <input id="name" type="text" class="form-control col-md-7 col-xs-12" name="problem" value="{{ old('problem') }}" required autofocus>
                              @if ($errors->has('problem'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('problem') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div>
                      <div class="form-group row{{ $errors->has('description') ? ' has-error' : '' }}">
                          <label for="description" class="control-label col-md-2">Description <span class="required">*</span></label>
                          <div class="col-md-10">
                              <input id="username" type="text" class="form-control col-md-7 col-xs-12" name="description" value="{{ old('description') }}" required>
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
                                            <a class="btn btn-danger btn-sm" href="{{ route('admin.problem.index') }}">Cancel</a>
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

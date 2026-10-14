<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-title">
                @lang('menus.backend.sidebar.general')
            </li>
            <li class="nav-item">
                <a class="nav-link {{
                    active_class(Active::checkUriPattern('admin/dashboard'))
                }}" href="{{ route('admin.dashboard') }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    @lang('menus.backend.sidebar.dashboard')
                </a>
            </li>

            <li class="nav-title">
                @lang('menus.backend.sidebar.system')
            </li>

            @if ($logged_in_user->isAdmin() || $logged_in_user->isServiceAdmin() || $logged_in_user->isStockAdmin())
                <li class="nav-item nav-dropdown {{
                    active_class(Active::checkUriPattern('admin/auth*'), 'open')
                }}">
                    <a class="nav-link nav-dropdown-toggle {{
                        active_class(Active::checkUriPattern('admin/auth*'))
                    }}" href="#">
                        <i class="nav-icon far fa-user"></i>
                        @lang('menus.backend.access.title')

                        @if ($pending_approval > 0)
                            <span class="badge badge-danger">{{ $pending_approval }}</span>
                        @endif
                    </a>

                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a class="nav-link {{
                                active_class(Active::checkUriPattern('admin/auth/user*'))
                            }}" href="{{ route('admin.auth.user.index') }}">
                                @lang('labels.backend.access.users.management')

                                @if ($pending_approval > 0)
                                    <span class="badge badge-danger">{{ $pending_approval }}</span>
                                @endif
                            </a>
                        </li>
                        @if ($logged_in_user->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{
                                active_class(Active::checkUriPattern('admin/auth/role*'))
                            }}" href="{{ route('admin.auth.role.index') }}">
                                @lang('labels.backend.access.roles.management')
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>

                @if ($logged_in_user->isAdmin())
                <li class="divider"></li>

                <li class="nav-item nav-dropdown {{
                    active_class(Active::checkUriPattern('admin/log-viewer*'), 'open')
                }}">
                        <a class="nav-link nav-dropdown-toggle {{
                            active_class(Active::checkUriPattern('admin/log-viewer*'))
                        }}" href="#">
                        <i class="nav-icon fas fa-list"></i> @lang('menus.backend.log-viewer.main')
                    </a>

                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a class="nav-link {{
                            active_class(Active::checkUriPattern('admin/log-viewer'))
                        }}" href="{{ route('log-viewer::dashboard') }}">
                                @lang('menus.backend.log-viewer.dashboard')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{
                            active_class(Active::checkUriPattern('admin/log-viewer/logs*'))
                        }}" href="{{ route('log-viewer::logs.list') }}">
                                @lang('menus.backend.log-viewer.logs')
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
            @endif

            <!-- Append by: Vutha -->
            @if ($logged_in_user->isServiceAdmin() || $logged_in_user->isAdmin() || $logged_in_user->isStockAdmin())
                <li class="nav-item nav-dropdown {{
                    active_class(Active::checkUriPattern('admin/setup*'), 'open')
                }}">
                    <a class="nav-link nav-dropdown-toggle {{
                        active_class(Active::checkUriPattern('admin/setup*'))
                    }}" href="#">
                        <i class="nav-icon fa fa-cog"></i>
                        @lang('menus.backend.setup.title')

                        @if ($pending_approval > 0)
                            <span class="badge badge-danger">{{ $pending_approval }}</span>
                        @endif
                    </a>

                    <ul class="nav-dropdown-items">
                        <li class="nav-item ">
                            <a class="nav-link {{
                                active_class(Active::checkUriPattern('admin/setup/customer*'))
                            }}" href="{{ route('admin.customer.index') }}">
                                <i class="fas fa-circle-notch"></i> @lang('labels.backend.setup.menu.customer')

                                @if ($pending_approval > 0)
                                    <span class="badge badge-danger">{{ $pending_approval }}</span>
                                @endif
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{
                                active_class(Active::checkUriPattern('admin/setup/vendor*'))
                            }}" href="{{ route('admin.vendor.index') }}">
                                <i class="fas fa-circle-notch"></i> @lang('labels.backend.setup.menu.vendor')

                                @if ($pending_approval > 0)
                                    <span class="badge badge-danger">{{ $pending_approval }}</span>
                                @endif
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{
                                active_class(Active::checkUriPattern('admin/auth/user*'))
                            }}" href="{{ route('admin.problem.index') }}">
                                <i class="fas fa-circle-notch"></i> @lang('labels.backend.setup.menu.problem')

                                @if ($pending_approval > 0)
                                    <span class="badge badge-danger">{{ $pending_approval }}</span>
                                @endif
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{
                                active_class(Active::checkUriPattern('admin/setup/category*'))
                            }}" href="{{ route('admin.category.index') }}">
                                <i class="fas fa-circle-notch"></i> @lang('labels.backend.setup.menu.category')

                                @if ($pending_approval > 0)
                                    <span class="badge badge-danger">{{ $pending_approval }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{
                                active_class(Active::checkUriPattern('admin/setup/brand*'))
                            }}" href="{{ route('admin.brand.index') }}">
                                <i class="fas fa-circle-notch"></i> @lang('labels.backend.setup.menu.brand')

                                @if ($pending_approval > 0)
                                    <span class="badge badge-danger">{{ $pending_approval }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{
                                active_class(Active::checkUriPattern('admin/setup/rmacode*'))
                            }}" href="{{ route('admin.rmacode.index') }}">
                                <i class="fas fa-circle-notch"></i> @lang('labels.backend.setup.menu.rmacode')

                                @if ($pending_approval > 0)
                                    <span class="badge badge-danger">{{ $pending_approval }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{
                                active_class(Active::checkUriPattern('admin/setup/enginner_busy*'))
                            }}" href="{{ route('admin.enginner_busy.index') }}">
                                <i class="fas fa-circle-notch"></i> @lang('labels.backend.setup.menu.enginner_busy')

                                @if ($pending_approval > 0)
                                    <span class="badge badge-danger">{{ $pending_approval }}</span>
                                @endif
                            </a>
                        </li>
                      </ul>
                        </li>
        @endif

        @if ($logged_in_user->isServiceAdmin() || $logged_in_user->isAdmin() || $logged_in_user->isServiceReceptionist() || $logged_in_user->isServiceEnginner())
                        <li class="nav-item nav-dropdown {{
                            active_class(Active::checkUriPattern('admin/service*'), 'open')


                        }} {{   active_class(Active::checkUriPattern('admin/shipping*'), 'open') }}
                           {{   active_class(Active::checkUriPattern('admin/transfer*'), 'open') }}
                        ">

                            <a class="nav-link nav-dropdown-toggle {{
                                active_class(Active::checkUriPattern('admin/service*') )
                            }} {{
                                active_class(Active::checkUriPattern('admin/shipping*') )
                            }}

                            {{
                                active_class(Active::checkUriPattern('admin/transfer*') )
                            }}
                            " href="#">
                                <i class="nav-icon fa fa-chart-line"></i>
                                @lang('menus.backend.service.title')

                                @if ($pending_approval > 0)
                                    <span class="badge badge-danger">{{ $pending_approval }}</span>
                                @endif
                            </a>


                            <ul class="nav-dropdown-items">
                              <li class="nav-item">
                                  <a class="nav-link {{
                                      active_class(Active::checkUriPattern('admin/service/'))
                                  }}" href="{{ route('admin.service.index') }}">
                                      <i class="fas fa-caret-right"></i> @lang('labels.backend.service.menu.service')

                                      @if ($pending_approval > 0)
                                          <span class="badge badge-danger">{{ $pending_approval }}</span>
                                      @endif
                                  </a>
                              </li>

                              <!--
                              <li class="nav-item">
                                  <a class="nav-link {{
                                      active_class(Active::checkUriPattern('admin/serviceshipping*'))
                                  }}" href="{{ route('admin.serviceshipping.index') }}">
                                      <i class="fas fa-caret-right"></i> @lang('labels.backend.service.menu.shipping')

                                      @if ($pending_approval > 0)
                                          <span class="badge badge-danger">{{ $pending_approval }}</span>
                                      @endif
                                  </a>
                              </li>
                            -->
                              @if(!$logged_in_user->isServiceEnginner())
                              <li class="nav-item">
                                  <a class="nav-link {{
                                      active_class(Active::checkUriPattern('admin/service/create*'))
                                  }}" href="{{ route('admin.service.create') }}">
                                      <i class="fas fa-caret-right"></i> @lang('labels.backend.service.menu.new_service')

                                      @if ($pending_approval > 0)
                                          <span class="badge badge-danger">{{ $pending_approval }}</span>
                                      @endif
                                  </a>
                              </li>


                              @endif

                              @if($logged_in_user->isServiceAdmin() || $logged_in_user->isAdmin())
                              <li class="nav-item">
                                  <a class="nav-link {{
                                      active_class(Active::checkUriPattern('admin/service/rma_paid*'))
                                  }}" href="{{ route('admin.service.rma_paid') }}">
                                      <i class="fas fa-caret-right"></i> @lang('labels.backend.service.menu.rma_paid')

                                      @if ($pending_approval > 0)
                                          <span class="badge badge-danger">{{ $pending_approval }}</span>
                                      @endif
                                  </a>
                              </li>
                              @endif
                              @if($logged_in_user->isServiceReceptionist() || $logged_in_user->isServiceAdmin() || $logged_in_user->isAdmin())
                              <li class="nav-item">
                                  <a class="nav-link {{
                                      active_class(Active::checkUriPattern('admin/service/calling*'))
                                  }}" href="{{ route('admin.service.calling') }}">
                                      <i class="fas fa-caret-right"></i> @lang('labels.backend.service.menu.calling')

                                      @if ($pending_approval > 0)
                                          <span class="badge badge-danger">{{ $pending_approval }}</span>
                                      @endif
                                  </a>
                              </li>
                              @endif
                              @if($logged_in_user->isServiceAdmin() || $logged_in_user->isAdmin())
                              <li class="nav-item">
                                  <a class="nav-link {{
                                      active_class(Active::checkUriPattern('admin/shipping*'))
                                  }}" href="{{ route('admin.service.shipping',['id'=>'create']) }}">
                                      <i class="fas fa-caret-right"></i> @lang('labels.backend.service.menu.shipping')

                                      @if ($pending_approval > 0)
                                          <span class="badge badge-danger">{{ $pending_approval }}</span>
                                      @endif
                                  </a>
                              </li>
                              @endif

                              @if($logged_in_user->isServiceAdmin() || $logged_in_user->isAdmin())
                              <li class="nav-item">
                                  <a class="nav-link {{
                                      active_class(Active::checkUriPattern('admin/transfer'))
                                  }}" href="{{ route('admin.service.transfer',['id'=>'create']) }}">
                                      <i class="fas fa-caret-right"></i> @lang('labels.backend.service.menu.transfer')

                                      @if ($pending_approval > 0)
                                          <span class="badge badge-danger">{{ $pending_approval }}</span>
                                      @endif
                                  </a>
                              </li>

                              <li class="nav-item">
                                  <a class="nav-link {{
                                      active_class(Active::checkUriPattern('admin/transfer/clearance*'))
                                  }}" href="{{ route('admin.service.transfer_clearance') }}">
                                      <i class="fas fa-caret-right"></i> @lang('labels.backend.service.menu.transfer_clearance')

                                      @if ($pending_approval > 0)
                                          <span class="badge badge-danger">{{ $pending_approval }}</span>
                                      @endif
                                  </a>
                              </li>

                              <li class="nav-item">
                                  <a class="nav-link {{
                                      active_class(Active::checkUriPattern('admin/service/cashtransfer*'))
                                  }}" href="{{ route('admin.service.cash_transfer') }}">
                                      <i class="fas fa-caret-right"></i> @lang('labels.backend.service.menu.cashtransfer')

                                      @if ($pending_approval > 0)
                                          <span class="badge badge-danger">{{ $pending_approval }}</span>
                                      @endif
                                  </a>
                              </li>
                              @endif
                            </ul>
                          </li>
                @endif
                @if($logged_in_user->isServiceAdmin() || $logged_in_user->isAdmin() || $logged_in_user->isServiceReceptionist() || $logged_in_user->isStockAdmin() || $logged_in_user->isStockPurchase() || $logged_in_user->isStockSaleorder())
                          <li class="nav-item nav-dropdown {{
                              active_class(Active::checkUriPattern('admin/report*'), 'open')
                          }}">
                              <a class="nav-link nav-dropdown-toggle {{
                                  active_class(Active::checkUriPattern('admin/report*'))
                              }}" href="#">
                                  <i class="nav-icon fa fa-paste"></i>
                                  @lang('menus.backend.report.title')

                                  @if ($pending_approval > 0)
                                      <span class="badge badge-danger">{{ $pending_approval }}</span>
                                  @endif
                              </a>
                              <ul class="nav-dropdown-items">

                                <li class="nav-item">
                                    <a class="nav-link {{
                                        active_class(Active::checkUriPattern('admin/service/reports*'))
                                    }}" href="{{ route('admin.service.report') }}">
                                        <i class="fas fa-caret-right"></i> @lang('menus.backend.report.submenu.service_report')

                                        @if ($pending_approval > 0)
                                            <span class="badge badge-danger">{{ $pending_approval }}</span>
                                        @endif
                                    </a>
                                </li>
                                @if(!$logged_in_user->isStockSaleorder())
                                <li class="nav-item">
                                    <a class="nav-link {{
                                        active_class(Active::checkUriPattern('admin/agin/report*'))
                                    }}" href="{{ route('admin.agin.report') }}">
                                        <i class="fas fa-caret-right"></i> @lang('menus.backend.report.submenu.agin_report')

                                        @if ($pending_approval > 0)
                                            <span class="badge badge-danger">{{ $pending_approval }}</span>
                                        @endif
                                    </a>
                                </li>
                                @endif
                                @if($logged_in_user->isServiceReceptionist() || $logged_in_user->isServiceAdmin() || $logged_in_user->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{
                                        active_class(Active::checkUriPattern('admin/selling/report*'))
                                    }}" href="{{ route('admin.selling.report') }}">
                                        <i class="fas fa-caret-right"></i> @lang('menus.backend.report.submenu.selling_report')

                                        @if ($pending_approval > 0)
                                            <span class="badge badge-danger">{{ $pending_approval }}</span>
                                        @endif
                                    </a>
                                </li>
                                @endif
                                @if($logged_in_user->isServiceAdmin() || $logged_in_user->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{
                                        active_class(Active::checkUriPattern('admin/report/revenue*'))
                                    }}" href="{{ route('admin.revenue.report') }}">
                                        <i class="fas fa-caret-right"></i> Revenue & Profit Report
                                    </a>
                                </li>
                                @endif

                                @if(!$logged_in_user->isStockPurchase() && !$logged_in_user->isServiceReceptionist())
                                <li class="nav-item">
                                    <a class="nav-link {{
                                        active_class(Active::checkUriPattern('admin/rma/report*'))
                                    }}" href="{{ route('admin.rma.report') }}">
                                        <i class="fas fa-caret-right"></i> @lang('menus.backend.report.submenu.rma_report')

                                        @if ($pending_approval > 0)
                                            <span class="badge badge-danger">{{ $pending_approval }}</span>
                                        @endif
                                    </a>
                                </li>


                              <li class="nav-item">
                                  <a class="nav-link {{
                                      active_class(Active::checkUriPattern('admin/shipping/report*'))
                                  }}" href="{{ route('admin.shipping.report') }}">
                                      <i class="fas fa-caret-right"></i> @lang('menus.backend.report.submenu.shipping_report')

                                      @if ($pending_approval > 0)
                                          <span class="badge badge-danger">{{ $pending_approval }}</span>
                                      @endif
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a class="nav-link {{
                                      active_class(Active::checkUriPattern('admin/transfer/report*'))
                                  }}" href="{{ route('admin.transfer.report') }}">
                                      <i class="fas fa-caret-right"></i> @lang('menus.backend.report.submenu.transfer_report')

                                      @if ($pending_approval > 0)
                                          <span class="badge badge-danger">{{ $pending_approval }}</span>
                                      @endif
                                  </a>
                              </li>
                              @endif

                              <li class="nav-item">
                                  <a class="nav-link {{
                                      active_class(Active::checkUriPattern('admin/cash/report*'))
                                  }}" href="{{ route('admin.cash.report') }}">
                                      <i class="fas fa-caret-right"></i> @lang('menus.backend.report.submenu.cash_report')

                                      @if ($pending_approval > 0)
                                          <span class="badge badge-danger">{{ $pending_approval }}</span>
                                      @endif
                                  </a>
                              </li>
                              </ul>
                            </li>
                      @endif
        </ul>
    </nav>

    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div><!--sidebar-->

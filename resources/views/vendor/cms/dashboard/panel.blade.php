<li class="nav-item">
    <a class="nav-link" href="{{ URL::to('/') }}"><span class="fa fa-arrow-left"></span> Back To Site </a>
</li>

<li class="nav-item @if (Request::is(cms()->backendRoute.'/dashboard')) active @endif">
    <a class="nav-link" href="{!! url(cms()->backendRoute.'/dashboard') !!}"><span class="fa fa-fw fa-line-chart"></span> Dashboard</a>
</li>

<li class="nav-item @if (Request::is(cms()->backendRoute.'/help')) active @endif">
    <a class="nav-link" href="{!! url(cms()->backendRoute.'/help') !!}"><span class="fa fa-fw fa-info-circle"></span> Help</a>
</li>

{{--@if (Route::get('user/settings'))
    <li class="nav-item @if (Request::is('user/settings') || Request::is('user/password')) active @endif">
        <a class="nav-link" href="{!! url('user/settings') !!}"><span class="fa fa-fw fa-wrench"></span> Settings</a>
    </li>
@endif--}}

@if (in_array('images', Config::get('cms.active-core-modules', Cms::defaultModules())))
    <li class="nav-item @if (Request::is(cms()->backendRoute.'/images') || Request::is(cms()->backendRoute.'/images/*')) active @endif">
        <a class="nav-link" href="{!! url(cms()->backendRoute.'/images') !!}"><span class="fa fa-fw fa-image"></span> Images</a>
    </li>
@endif

@if (in_array('files', Config::get('cms.active-core-modules', Cms::defaultModules())))
    <li class="nav-item @if (Request::is(cms()->backendRoute.'/files') || Request::is(cms()->backendRoute.'/files/*')) active @endif">
        <a class="nav-link" href="{!! url(cms()->backendRoute.'/files') !!}"><span class="fa fa-fw fa-file"></span> Files</a>
    </li>
@endif


@if (in_array('pages', Config::get('cms.active-core-modules', Cms::defaultModules())))
    <li class="nav-item @if (Request::is(cms()->backendRoute.'/pages') || Request::is(cms()->backendRoute.'/pages/*')) active @endif">
        <a class="nav-link" href="{!! url(cms()->backendRoute.'/pages') !!}"><span class="fa fa-fw fa-file-text"></span> Pages</a>
    </li>
@endif




<li class="nav-item @if (Request::is(cms()->backendRoute.'/companies')) active @endif">
    <a class="nav-link" href="{!! url(cms()->backendRoute.'/companies') !!}"><span class="fa fa-fw fa-info-circle"></span> Companies</a>
</li>


{!! ModuleService::menus() !!}

{!! Cms::packageMenus() !!}

@if (Route::get('admin/users'))
    <li class="sidebar-header"><span>Admin</span></li>
@endif

@if (Route::get('admin/dashboard'))
    <li class="nav-item @if (Request::is('admin/dashboard') || Request::is('admin/dashboard/*')) active @endif">
        <a class="nav-link" href="{!! url('admin/dashboard') !!}"><span class="fa fa-fw fa-tachometer"></span> Dashboard</a>
    </li>
@endif
@if (Route::get('admin/users'))
    <li class="nav-item @if (Request::is('admin/users') || Request::is('admin/users/*')) active @endif">
        <a class="nav-link" href="{!! url('admin/users') !!}"><span class="fa fa-fw fa-users"></span> Users</a>
    </li>
@endif
@if (Route::get('admin/roles'))
    <li class="nav-item @if (Request::is('admin/roles') || Request::is('admin/roles/*')) active @endif">
        <a class="nav-link" href="{!! url('admin/roles') !!}"><span class="fa fa-fw fa-lock"></span> Roles</a>
    </li>
@endif

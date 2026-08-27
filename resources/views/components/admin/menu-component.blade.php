@if(count($plugin) > 0) 
@foreach ($plugin as $plug)
@if(count($plug->menu) > 0)
@if(count($plug->menu) > 1)
<li>
    <a href="#{{$plug->code}}" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="fas {{$plug->plugin_icon}}"></i><span>{{$plug->name}}</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
    <ul id="{{$plug->code}}" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
        @foreach ($plug->menu as $menu)
        @php
        $role = 0;
        if($menu->permission_id != null) {
        $permission_name = $menu->permission->name ?? false;
        if($permission_name != false) {
        $role = 1;
        }
        }
        @endphp

        @if($role != 0)
        @can($permission_name)
        <li class="submenu-item">
            <a href="{{ route($menu->route_link) }}">{{$menu->name}}</a>
        </li>
        @endcan
        @else
        <li class="submenu-item">
            <a href="{{ route($menu->route_link) }}">{{$menu->name}}</a>
        </li>
        @endif
        @endforeach
    </ul>
</li> 
@else
@foreach ($plug->menu as $menu)
<li><a href="{{ route($menu->route_link) }}" class="iq-waves-effect"><i class="ri-message-line"></i><span>{{$menu->name}}</span></a></li> 
@endforeach
@endif
@endif
@endforeach
@endif
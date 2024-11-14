@extends('layouts.dashboard')
@section('menu')
    @if($user::UserType() == "staff")
        @forelse($menu_headers as $menu_header)
            @if($role->menu_items->whereIn("id",$menu_header->items->pluck("id"))->isNotEmpty())
                <li class="nav-item mb-1">
                    <button class="w-100 btn btn-toggle align-items-center justify-content-between rounded collapsed p-0" data-bs-toggle="collapse" data-bs-target="#{{$menu_header->slug}}-collapse" @if(Route::is($menu_header->items->pluck("all_actions"))) aria-expanded="true" @else aria-expanded="false" @endif>
                        <div class="d-flex align-items-center justify-content-start">
                            @if($menu_header->icon)
                                <i class="menu-header-icon vertical-middle {{$menu_header->icon}} fa-1-8x me-3"></i>
                            @else
                                <i class="menu-header-icon vertical-middle fad fa-question fa-1-8x me-3"></i>
                            @endif
                            <span class="menu-header-text">{{ $menu_header->name }}</span>
                        </div>
                        <i class="fa fa-angle-left menu-header-arrow fa-1-2x"></i>
                    </button>
                    @if($menu_header->icon)
                        <i class="menu-header-icon small-sidebar-icon vertical-middle {{$menu_header->icon}} fa-1-8x me-3"></i>
                    @else
                        <i class="menu-header-icon small-sidebar-icon vertical-middle fad fa-question fa-1-8x me-3"></i>
                    @endif
                    <div id="{{ $menu_header->slug }}-collapse" class="collapse iransans rounded-2 bg-menu-dark-light p-2 @if(Route::is($role->menu_items()->whereIn("menu_items.id",$role->menu_items()->where("menu_items.menu_header_id",$menu_header->id)->pluck("role_menu.menu_item_id"))->pluck("role_menu.route"))) show @endif">
                        <ul class="btn-toggle-nav list-unstyled fw-normal small">
                            @if($role->menu_items->whereIn("id",$menu_header->items->pluck("id"))->isNotEmpty())
                                @foreach($menu_header->items as $item)
                                    @if($item->children->isNotEmpty())
                                        @if($role->menu_items->whereIn("id",$item->children->pluck("id"))->isNotEmpty())
                                            <li class="menu-item-container">
                                                <button class="w-100 btn btn-toggle menu-item-toggle align-items-center justify-content-between rounded collapsed p-2" data-bs-toggle="collapse" data-bs-target="#item-{{ $item->id }}-collapse" @if(Route::is($role->menu_items()->whereIn("menu_items.id",$item->children->pluck("id"))->pluck("role_menu.route"))) aria-expanded="true" @else aria-expanded="false" @endif>
                                                    <span class="menu-item-text iransans">{{$item->name}}</span>
                                                    <i class="fa fa-angle-left menu-header-arrow"></i>
                                                </button>
                                                <div id="item-{{ $item->id }}-collapse" class="collapse menu-item @if(Route::is($role->menu_items()->whereIn("menu_items.id",$item->children->pluck("id"))->pluck("role_menu.route"))) show @endif">
                                                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                                        @foreach($item->children as $child)
                                                            @if($role->menu_items->where("id",$child->id)->isNotEmpty())
                                                                <li class="ms-3 p-2 @if(Route::is($role->menu_items()->where("menu_items.id",$child->id)->pluck("role_menu.route"))) active @endif">
                                                                    <a href="{{route($role->menu_items->where("pivot.menu_item_id",$child->id)->where("pivot.menu_action_id",$child->actions->where("action",$child->main_route)->first()->id)->first()->pivot->route)}}" class="menu-item-link rounded @if(Route::is($role->menu_items()->where("menu_items.id",$child->id)->pluck("role_menu.route"))) active @endif">
                                                                        {{$child->name}}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </li>
                                        @endif
                                    @else
                                        @if($role->menu_items->where("id",$item->id)->isNotEmpty() && $item->parent_id == null)
                                            <li class="ms-3 p-2 @if(Route::is($role->menu_items()->where("menu_items.id",$item->id)->pluck("role_menu.route"))) active @endif">
                                                <a href="{{route($role->menu_items->where("pivot.menu_item_id",$item->id)->where("pivot.menu_action_id",$item->actions->where("action",$item->main_route)->first()->id)->first()->pivot->route)}}" class="menu-item-link rounded iransans @if(Route::is($role->menu_items()->where("menu_items.id",$item->id)->pluck("role_menu.route"))) active @endif">
                                                    {{$item->name}}
                                                </a>
                                            </li>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </li>
            @endif
        @empty
        @endforelse
    @elseif($user::UserType() == "admin")
        @forelse($menu_headers as $menu_header)
            <li class="nav-item mb-1">
                <button class="w-100 btn btn-toggle align-items-center justify-content-between rounded collapsed p-0" data-bs-toggle="collapse" data-bs-target="#{{$menu_header->slug}}-collapse" @if(Route::is($menu_header->items->pluck("all_actions"))) aria-expanded="true" @else aria-expanded="false" @endif>
                    <div class="d-flex align-items-center justify-content-start">
                        @if($menu_header->icon)
                            <i class="menu-header-icon vertical-middle {{$menu_header->icon}} fa-1-8x me-3"></i>
                        @else
                            <i class="menu-header-icon vertical-middle far fa-question fa-1-8x me-3"></i>
                        @endif
                        <span class="menu-header-text">{{ $menu_header->name }}</span>
                    </div>
                    <i class="fa fa-angle-left menu-header-arrow fa-1-2x"></i>
                </button>
                @if($menu_header->icon)
                    <i class="menu-header-icon small-sidebar-icon vertical-middle {{$menu_header->icon}} fa-1-8x me-2"></i>
                @else
                    <i class="menu-header-icon small-sidebar-icon vertical-middle far fa-question fa-1-8x me-2"></i>
                @endif
                <div id="{{ $menu_header->slug }}-collapse" class="collapse iransans rounded-2 bg-menu-dark-light p-2 @if(Route::is($menu_header->items->pluck("all_actions"))) show @endif">
                    <ul class="btn-toggle-nav list-unstyled fw-normal small">
                        @foreach($menu_header->items as $item)
                            @if($item->children->isNotEmpty())
                                <li class="menu-item-container">
                                    <button class="w-100 btn btn-toggle menu-item-toggle align-items-center justify-content-between rounded collapsed p-2" data-bs-toggle="collapse" data-bs-target="#item-{{ $item->id }}-collapse" @if(Route::is($item->children->pluck("all_actions"))) aria-expanded="true" @else aria-expanded="false" @endif>
                                        <span class="menu-item-text iransans">{{$item->name}}</span>
                                        <i class="fa fa-angle-left menu-header-arrow"></i>
                                    </button>
                                    <div id="item-{{ $item->id }}-collapse" class="collapse menu-item @if(Route::is($item->children->pluck("all_actions"))) show @endif">
                                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                            @foreach($item->children as $child)
                                                <li class="ms-3 p-2 @if(Route::is($child->action_route)) active @endif">
                                                    <a href="{{route($child->action_route)}}" class="menu-item-link rounded @if(Route::is($child->action_route)) active @endif">
                                                        {{$child->name}}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            @else
                                @if($menu_header->items->where("id",$item->id)->isNotEmpty() && $item->parent_id == null)
                                    <li class="p-2 @if(Route::is($item->action_route)) active @endif">
                                        <a href="{{route($item->action_route)}}" class="menu-item-link rounded iransans @if(Route::is($item->action_route)) active @endif">
                                            {{$item->name}}
                                        </a>
                                    </li>
                                @endif
                            @endif
                        @endforeach
                    </ul>
                </div>
            </li>
        @empty
        @endforelse
    @else
        {{abort(403)}}
    @endif
@endsection
@section('main')
    @if(Route::is("staff_idle"))
        <div style="position: absolute;top: 0;left: 0;right: 0;bottom: 0" class="d-flex align-items-center justify-content-center">
            <img alt="همیاران شمال شرق" style="width: 20vw;height: auto" src="{{ asset("/images/idle-bg.png") }}">
        </div>
    @else
        <div class="content-header position-fixed">
            @yield('header')
        </div>
        <div class="content-page bg-white w-100">
            @yield('content')
        </div>
        <div class="content-footer position-fixed">
            @yield('footer')
        </div>
    @endif
@endsection

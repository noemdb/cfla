<li class="nav-item dropdown p-1">
    <div class="btn-group float-right" role="group" aria-label="Icons users">
        <a class="nav-link text-primary" href="#">
            <i class="{{ $icon_menus['messege'] ?? '' }} "></i>
        </a>
        <a class="nav-link text-info" href="{{ route('alerts.index') }}">
            <i class="{{ $icon_menus['alert'] ?? '' }} "></i>
        </a>
        <a class="nav-link text-danger" href="{{ route('tasks.index') }}">
            <i class="{{ $icon_menus['task'] ?? '' }} "></i>
        </a>
    </div>                
</li>
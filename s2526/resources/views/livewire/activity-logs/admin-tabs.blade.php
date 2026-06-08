<div>
    <nav>
        <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
            <button class="nav-link p-1 {{ $activeTab === 'dashboard' ? 'active' : '' }}"
                    wire:click="setActiveTab('dashboard')"
                    type="button">
                <i class="fas fa-chart-line me-1"></i>
            </button>

            <button class="nav-link p-1 {{ $activeTab === 'details' ? 'active' : '' }}"
                    wire:click="setActiveTab('details')"
                    type="button">
                <i class="fa fa-history" aria-hidden="true"></i>
            </button>

            <button class="nav-link p-1 {{ $activeTab === 'system-logs' ? 'active' : '' }}"
                    wire:click="setActiveTab('system-logs')"
                    type="button">
                <i class="fas fa-bug me-1"></i>
            </button>

            <button class="nav-link p-1 {{ $activeTab === 'resend-emails' ? 'active' : '' }}"
                    wire:click="setActiveTab('resend-emails')"
                    type="button">
                <i class="fas fa-envelope me-1"></i>
            </button>
        </div>
    </nav>

    <div class="tab-content border border-top-0 bg-white" id="nav-tabContent">
        <div class="tab-pane fade {{ $activeTab === 'dashboard' ? 'show active' : '' }}"
            id="nav-dashboard"
            role="tabpanel">
            <div class="p-1 h-100">
                @if($activeTab === 'dashboard')
                    <livewire:activity-logs.dashboard />
                @endif
            </div>
        </div>

        <div class="tab-pane fade {{ $activeTab === 'details' ? 'show active' : '' }}"
            id="nav-details"
            role="tabpanel">
            <div class="p-1">
                @if($activeTab === 'details')
                    <livewire:activity-logs.table />
                @endif
            </div>
        </div>

        <div class="tab-pane fade {{ $activeTab === 'system-logs' ? 'show active' : '' }}"
            id="nav-system-logs"
            role="tabpanel">
            <div class="p-1">
                @if($activeTab === 'system-logs')
                    <livewire:activity-logs.system-logs />
                @endif
            </div>
        </div>

        <div class="tab-pane fade {{ $activeTab === 'resend-emails' ? 'show active' : '' }}"
            id="nav-resend-emails"
            role="tabpanel">
            <div class="p-1">
                @if($activeTab === 'resend-emails')
                    <livewire:activity-logs.resend-emails />
                @endif
            </div>
        </div>
    </div>
</div>

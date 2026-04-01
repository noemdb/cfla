@if($flashAlert)
    @php
        $alertStyles = [
            'warning' => 'bg-yellow-900/60 border border-yellow-600 text-yellow-300',
            'success' => 'bg-green-900/60 border border-green-600 text-green-300',
            'info'    => 'bg-blue-900/60 border border-blue-600 text-blue-300',
        ];
        $iconMap = [
            'warning' => 'exclamation-triangle',
            'success' => 'check-circle',
            'info'    => 'information-circle',
        ];
        $style = $alertStyles[$flashAlert['type']] ?? $alertStyles['info'];
        $icon  = $iconMap[$flashAlert['type']] ?? 'information-circle';
    @endphp
    <div class="mb-4 flex items-start gap-3 rounded-xl px-4 py-3 text-sm font-medium {{ $style }}">
        <x-icon :name="$icon" class="mt-0.5 h-5 w-5 shrink-0" />
        <span>{{ $flashAlert['message'] }}</span>
    </div>
@endif

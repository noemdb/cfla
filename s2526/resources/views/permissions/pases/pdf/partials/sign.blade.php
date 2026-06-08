<table class="table-sm" style="width: 100%;font-size:0.7rem;margin-top: 1rem;">
    <tr>
        @if ($pase->require_auhtorize_guardian)
            <td> <p>&nbsp;</p> {{ $representant->name ?? null}}<br> <span class="text-muted">Representante</span> </td>            
        @endif
        @if ($pase->require_auhtorize_teacher)
            <td> <p>&nbsp;</p> {{ $profesor->fullname ?? null}}<br> <span class="text-muted">Profesor</span> </td>            
        @endif
        @if ($pase->require_auhtorize_manager)
            @php $manager = ($pase) ? $pase->manager : null @endphp
            <td> <p>&nbsp;</p> {{ ($manager) ? $manager->fullname : null}}<br> <span class="text-muted">Coordinador</span> </td>            
        @endif
    </tr>
</table>
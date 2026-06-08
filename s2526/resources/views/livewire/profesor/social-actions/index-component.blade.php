<div>
    <nav style="font-size: 1.1rem !important">
        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
            <a class="nav-item nav-link {{($tabActivity) ? 'active' : null}}" wire:click="updateCommunityAction()" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true"><span class=" font-weight-bold">Servicios Ejecutados</span></a>
            <a class="nav-item nav-link {{($tabAsistent) ? 'active' : null}}" wire:click="updateCommunityHour()" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false"><span class=" font-weight-bold">Asistencia</span></a>
            <a class="nav-item nav-link {{($tabCalendar) ? 'active' : null}}" id="nav-actions-tab" data-toggle="tab" href="#nav-actions" role="tab" aria-controls="nav-actions" aria-selected="false"><span class=" font-weight-bold">Calendario</span></a>
            <a class="nav-item nav-link {{($tabEstudiant) ? 'active' : null}}" wire:click="updateEstudiant()" id="nav-estudiant-tab" data-toggle="tab" href="#nav-estudiant" role="tab" aria-controls="nav-estudiant" aria-selected="true"><span class=" font-weight-bold">Estudiantes</span></a>
        </div> 
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade border border-top-0 rounded-bottom p-2 {{($tabActivity) ? 'show active' : null}} " id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
            <livewire:profesor.social-actions.community-action.index-component />
        </div>        
        <div class="tab-pane fade border border-top-0 rounded-bottom p-2 {{($tabAsistent) ? 'show active' : null}}" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
            <livewire:profesor.social-actions.community-hour.index-component />
        </div>
        <div class="tab-pane fade border border-top-0 rounded-bottom p-2 {{($tabCalendar) ? 'show active' : null}}" id="nav-actions" role="tabpanel" aria-labelledby="nav-actions-tab">
            <livewire:profesor.social-actions.calendar.index-component />
        </div>
        <div class="tab-pane fade border border-top-0 rounded-bottom p-2 {{($tabEstudiant) ? 'show active' : null}}" id="nav-estudiant" role="tabpanel" aria-labelledby="nav-estudiant-tab">
            <livewire:profesor.social-actions.estudiant.index-component />
            {{-- lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatum. --}}
        </div>
    </div>
</div>
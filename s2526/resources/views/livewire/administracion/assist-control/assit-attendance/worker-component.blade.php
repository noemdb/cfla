<div>

    {{-- @include('livewire.elements.messeges.oper_ok') --}}
    <div class="container-fluid">
        <div class="row">
            <div class="col">

                @include('livewire.administracion.assist-control.assit-attendance.table.workers')
                
            </div>
            @if ($editWorkerModeAssitSchedules)
                <div class="col" style="position:">
                    {{-- <div class="fixed-top"> --}}
                    <div>
                        <div class=" border rounded shadow" style="width: auto; position: fixed; border-radius: 0;height: auto;">
                        {{-- <div class=" border rounded shadow"> --}}

                            @include('livewire.administracion.assist-control.assit-attendance.form.workers')

                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

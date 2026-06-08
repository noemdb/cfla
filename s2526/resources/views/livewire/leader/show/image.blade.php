{{-- <div class="continer p-4"> --}}


    {{-- <div class="col-lg-auto col-xl-auto"> --}}
        {{-- <div class="shadow-lg p-4 m-4"> --}}

            {{-- <div class="">
    
                <div class="alert alert-warning alert-dismissible fade show" role="alert" wire:click="close()">
                    <strong><strong>{{$pevaluacion->asignatura_name}}</strong></strong>            
                    <button type="button" class="close" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
        
                <div class="mx-4 px-4">            
                    <span class=" font-weight-bold">{{$list_comment['title'] ?? ''}}</span>: <span class=" font-weight-normal"> {{$lesson->title ?? ''}}</span> <br>
                    <span class=" font-weight-bold">{{$list_comment['comments'] ?? ''}}</span>: <span class=" font-weight-normal"> {{$lesson->comments ?? ''}}</span>            
                </div>
            </div> --}}

            {{-- <div class="">
                <div class="text-center">
                    <img src="{{ asset($lesson->evidence_url) ?? null}}" class="img-fluid img-thumbnail " alt="">
                </div>
            </div> --}}
    
        {{-- </div> --}}
    {{-- </div> --}}

    
    
{{-- </div> --}}


{{-- <div class="row">
    <div class="col">
        <div class="col-lg-auto col-xl-auto">    
            <div class="table-secondary p-2 alert-dismissible fade show rounded" role="alert" wire:click="close()">
                <strong><strong>{{$pevaluacion->asignatura_name}}</strong></strong>            
                <button type="button" class="close" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
    
            <div class="mx-4 px-4">            
                <span class=" font-weight-bold">{{$list_comment['title'] ?? ''}}</span>: <span class=" font-weight-normal"> {{$lesson->title ?? ''}}</span> <br>
                <span class=" font-weight-bold">{{$list_comment['comments'] ?? ''}}</span>: <span class=" font-weight-normal"> {{$lesson->comments ?? ''}}</span>            
            </div>
        </div>
        
        <img src="{{ asset($lesson->evidence_url) ?? null}}" class="img-fluid border rounded shadow-lg p-4 m-4" alt="">
    </div>
</div> --}}

<div class=" d-flex justify-content-center">
    <div class="row">
        <div class="col-auto">

            <div class="table-secondary py-2 alert-dismissible fade show rounded" role="alert" wire:click="close()">     
                &nbsp;                   
                <button type="button" class="close" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="border rounded border-top-0 shadow-sm pl-2">

                <div class="px-4">
                    <strong><strong>{{$pevaluacion->asignatura_name}}</strong></strong>
                </div>
                
                <div class="mx-4 px-4">            
                    <span class=" font-weight-bold">{{$list_comment['title'] ?? ''}}</span>: <span class=" font-weight-normal"> {{$lesson->title ?? ''}}</span> <br>
                    <span class=" font-weight-bold">{{$list_comment['comments'] ?? ''}}</span>: <span class=" font-weight-normal"> {{$lesson->comments ?? ''}}</span> <br>
                    <span class=" font-weight-bold">{{$list_comment['status'] ?? ''}}</span>: <span class=" font-weight-normal"> {{ ($lesson->status) ? 'SI' : 'NO' }}</span>            
                </div>

                <div class="d-flex justify-content-center">
                    <img src="{{ asset($lesson->evidence_url) ?? null}}" class="img-fluid border rounded shadow-lg p-4 m-4" alt="Imagen borrada">
                </div>


            </div>

        </div>
    </div>
</div>
  
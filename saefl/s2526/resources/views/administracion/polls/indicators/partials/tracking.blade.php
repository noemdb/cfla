<h5>Seguimiento sobre las preferencias de los participantes.</h5>

<nav>
    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
        @foreach($poll_questions as $poll_question)
            <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}} font-weight-bold text-uppercase small"
                id="nav-header-tab-analyzers-{{$poll_question->id}}" data-toggle="tab"
                href="#nav-content-analyzers-{{$poll_question->id}}" role="tab" aria-controls="nav-home" aria-selected="true">
                {{$poll_question->text ?? ''}}
            </a>
        @endforeach
    </div>
</nav>

<div class="tab-content border border-top-0" id="nav-tabContent">                    
    @foreach ($poll_questions as $poll_question)
        @php $poll_options = $poll_question->poll_options @endphp
        <div class="tab-pane fade {{($loop->iteration==1) ? 'show active':''}}" id="nav-content-analyzers-{{$poll_question->id ?? ''}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$poll_question->id ?? ''}}">
            {{-- {{$poll_options ?? null}} --}}
            

            @include('administracion.polls.charts.tracking')

        </div>
    @endforeach
</div>


{{-- <nav>
  <div class="nav nav-tabs" id="nav-tab" role="tablist">
  	@foreach()
    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Home</a>
    @endforeach
  </div>
</nav>
<div class="tab-content" id="nav-tabContent">
  <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">...</div>
  <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">...</div>
  <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">...</div>
</div>
 --}}

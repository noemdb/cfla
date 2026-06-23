<div class="p-2 m-2 border rounded">

	<div class="p-2">
        <strong>{{ $loop->iteration ?? null }}: {{$poll_question->text ?? ''}}</strong>

        <div class="container">
            <div class="row">
                <div class="col-12 ">
                    @include('academicos.pollmains.charts.question')
                </div>
            </div>
        </div>


	</div>
</div>

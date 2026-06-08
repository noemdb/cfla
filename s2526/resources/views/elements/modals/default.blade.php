@php $modal_id = ($post) ? 'modalShowPost'.$post->id : 'exampleModal' @endphp
<!-- Button trigger modal -->
<a class="btn btn-lg btn-success btn-sm" href="{{ route('welcome',['post_id'=>$post->id]) }}" role="button" data-toggle="modal" data-target="#{{$modal_id}}">
    Mostrar
</a>

<!-- Modal -->
<div class="modal fade" id="{{$modal_id}}" tabindex="-1" role="dialog" aria-labelledby="{{$modal_id}}Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header py-1">
                {{-- <h5 class="modal-title">Modal title</h5> --}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> --}}
                {{-- <img src="{{ asset($imageUrl) }}" class="img-thumbnail rounded mx-auto d-block img-fluid"> --}}
                @include('blogs.content.welcome.post')
            </div>
        </div>
    </div>
</div>

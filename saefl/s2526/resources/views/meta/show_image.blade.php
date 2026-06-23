{{-- <img src="{{ $image_url ?? null}}" alt="Imagen recibida">
<div><strong>{{ $caption ?? null }}</strong></div> --}}

<!-- Button to trigger the modal -->
<button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#imageModal{{$mediaId ?? null}}">
    <i class="fas fa-image"></i> <!-- Icono de imagen -->
</button>

<!-- Modal Structure -->
<div class="modal fade" id="imageModal{{$mediaId ?? null}}" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Imagen recibida</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <!-- Placeholder for the image -->
                <img id="modalImage" src="{{ $image_url ?? null}}" alt="Imagen" class="img-fluid" style="max-height: 400px;">
                <p id="imageCaption" class="mt-3 text-muted"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div class="mx-auto max-w-sm">
    <h2 class="mb-2 text-3xl font-bold text-white">Paso 4</h2>
    <p class="mb-4 text-gray-400">Descarga tu planilla de registro y guarda el QR</p>

    @if ($emrollment_id)
    <form method="POST" action="#" class="space-y-2">
        @csrf

        <div class="grid md:grid-cols-1">

            <div class="space-y-2 mb-2">
                <x-button wire:click="downloadPDF({{$emrollment_id}})"  xl primary label="Descarga" class="w-full my-2" />
            </div>
            
            <div class="mt-5 text-center">
                <h3 class="text-md font-semibold">Escanea el código QR para descargar el PDF</h3>
                <div class="mt-2">{!! $this->generateQrCode($emrollment_id) !!}</div>
            </div>

        </div>

    </form>
    @else

    <div class="text-red-500">Ocurrieron errores</div>
        
    @endif

    

</div>

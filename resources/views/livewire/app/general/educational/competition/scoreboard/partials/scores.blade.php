@php $peducativos = $competition->peducativos; @endphp
<ul>
    @forelse ($peducativos as $peducativo)    

        <li class=" bg-lime-100 border-t-4 border-lime-600 p-2 my-2">
            <div class="text-lg font-bold text-end">{{$peducativo->name}}</div>

            @php $grados = $peducativo->grados; @endphp
            <ul>
                @forelse ($grados as $grado)
                    <li class="border-b-2 py-3">
                        <div class="flex justify-between text-md items-center">
                            <div class="">{{$grado->name}}</div>
                            <div class="font-bold text-lg">{{$competition->getTotalScoreForGrado($grado->id)}} <small class=" text-gray-300">Pts</small></div>
                        </div>
                        
                    </li>
                @empty
                    <li>no hay grados/años</li>
                @endforelse
            </ul>        
        </li>        
    @empty
        <li>no hay planes de estudio</li>
    @endforelse
</ul>
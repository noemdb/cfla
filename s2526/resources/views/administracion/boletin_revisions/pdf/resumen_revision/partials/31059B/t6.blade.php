@php $estudiant = $titulos_chunk->last()->estudiant; @endphp

<table cellpadding="2" cellspacing="2" border="1" width="100%" class="table-list">
   <thead>
      <tr>
         <th>TOTAL DE TITULOS EMITIDOS:</th>
         <th>{{ $titulos_chunk->count()}}</th>
         <th>AÑO:</th>
         <th>{{ $estudiant->grado->name ?? ''}}</th>
         <th>SECCIÓN:</th>
         <th>{{ $estudiant->seccion->name ?? ''}}</th>
      </tr>
   </thead>
</table>
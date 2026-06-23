@php $arrow = '<i class="fa fa-arrow-right" aria-hidden="true"></i>'; @endphp

@include('administracion.collections.coll_politicals.form.asistent.steps.start')

@php $list_comment = $list_comment_arr['coll_politicals'];@endphp
@include('administracion.collections.coll_politicals.form.asistent.steps.coll_politicals.fields.pescolar_id')
@include('administracion.collections.coll_politicals.form.asistent.steps.coll_politicals.fields.name')
@include('administracion.collections.coll_politicals.form.asistent.steps.coll_politicals.fields.group')
{{-- @include('administracion.collections.coll_politicals.form.asistent.steps.coll_politicals.fields.description') --}}
@include('administracion.collections.coll_politicals.form.asistent.steps.coll_politicals.fields.date')

{{-- @php $list_comment = $list_comment_arr['coll_nivels'];@endphp --}}
{{-- @include('administracion.collections.coll_politicals.form.asistent.steps.coll_nivels.fields.name') --}}

@php $list_comment = $list_comment_arr['coll_messeges'];@endphp
@include('administracion.collections.coll_politicals.form.asistent.steps.coll_messeges.fields.subject')
@include('administracion.collections.coll_politicals.form.asistent.steps.coll_messeges.fields.greeting')
@include('administracion.collections.coll_politicals.form.asistent.steps.coll_messeges.fields.sentence')
@include('administracion.collections.coll_politicals.form.asistent.steps.coll_messeges.fields.footer')

@include('administracion.collections.coll_politicals.form.asistent.steps.preview')

@include('administracion.collections.coll_politicals.form.asistent.steps.finish')

<div class="card {{$textalign ?? 'text-justify'}} bd-callout bd-callout-{{$class ?? ''}} mt-2">
  @isset($imgurl)
    <img class="card-img-top" src="{{$imgurl}}" alt="Card image cap">
  @endisset
  <div class="card-header">
    {{$header ?? ''}}
  </div>
  <div class="card-body">
    {{$body ?? ''}}
    @isset($buttomtext)
      <a href="{{$buttomurl ?? ''}}" class="btn btn-{{$btnclass ?? 'primary'}} float-right">{{$buttomtext ?? ''}}</a>
    @endisset
  </div>
  @isset($footertext)
  <div class="card-footer text-muted">
    {{$footertext ?? ''}}
  </div>
  @endisset
</div>
@foreach ($site->admin_actions as $action)
  @include('sites.partials.' . $action . '-btn')
  @if (!$loop->last)
    &nbsp;
  @endif
@endforeach

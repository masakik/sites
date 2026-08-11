<ul class="list-group">

  <li class="list-group-item">
    @include('sites.partials.logon-btn', ['loginData' => $site->login_data])
  </li>

  @can('admin')
    <li class="list-group-item">
      <div class="form-inline">
        @include('sites.partials.admin-btns')
      </div>
    </li>
  @endcan

</ul>

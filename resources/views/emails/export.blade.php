@extends('master')


@section('content')
@parent

<div class="card">
  <h5 class="card-header">Emails responsáveis - {{ count($owners_emails)}} </h5>
  <div class="card-body">
  {{ join(', ', $owners_emails) }} 
  </div>
</div>

<br><br>
<div class="card">
  <h5 class="card-header">Emails Adminstradores - {{ count($admins_emails)}} </h5>
  <div class="card-body">
  {{ join(', ', $admins_emails) }} 
  </div>
</div>

@endsection('content')
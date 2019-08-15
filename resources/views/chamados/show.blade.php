@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
@parent

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Aberto por</th>
        <th>Aberto em</th>
        <th>Status</th>
        <th>Tipo</th>
      </tr>
    </thead>

    <tbody>

      <tr>
        <td>{{ $chamado->user->name }}</td>
        <td>{{ Carbon\Carbon::parse($chamado->created_at)->format('d/m/Y H:i') }}</td>
        <td>{{ $chamado->status }}</td>
        <td>{{ $chamado->tipo }}</td>
        
      </tr>

</tbody>
</table>
</div>

<h2>Descrição</h2>
<p>{{ $chamado->descricao }}</p>

<h2>Comentários</h2>
<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Em</th>
        <th>Comentário</th>
        <th>Por</th>
      </tr>
    </thead>

    <tbody>

@forelse ($chamado->comentarios->sortBy('created_at') as $comentario)
      <tr>
        <td>{{ Carbon\Carbon::parse($comentario->created_at)->format('d/m/Y H:i') }}</td>
        <td>{{ $comentario->comentario }}</td>
        <td>{{ $comentario->user->name }}</td>
      </tr>
@empty
    <tr>
        <td colspan="3">Não há comentários</td>
    </tr>
@endforelse
</tbody>
</table>

</div>



  <form method="POST" role="form" action="/comentarios/{{$chamado->id}}/">
      @csrf

      <div class="form-group">
        <label for="comentario"><b>Novo comentário:</b></label>
        <textarea class="form-control" id="comentario" name="comentario" rows="7"></textarea>
      </div>


      <div class="form-group">
        <button type="submit" class="btn btn-primary">Enviar</button>
      </div>
  </form>

@stop


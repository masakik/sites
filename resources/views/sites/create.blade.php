@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
    <form method="POST" role="form" class="form-inline" action="/sites">
        @csrf
        <div class="form-group">
            <label for="dominio">Domínio:</label>
                <input name="dominio" class="form-control dominio" placeholder="meuqueridosite" id="dominio"> 
            <b> {{ $dnszone }} </b>
        
        </div>
        <br>
        <br>

        <div class="form-group">
            <label for="numeros_usp">Números USP: </label> 
            <textarea name="numeros_usp" class="form-control" rows="4"></textarea>             
        <br>Relação de números USP, separados por vírgula, que terão permissão para administrar o site.
        </div>
        </br>
        </br>
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>

@stop

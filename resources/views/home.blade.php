@extends('layouts.master')

@section('title', 'GAC System - home')

@section('content')
    <div class="row m-0 justify-content-center align-items-center h-75">
        <div class="col-auto mx-2">
            <div class="card bg-info">
                <div class="card-header">
                    <h1 class="text-center font-weight-bold">Página em construção
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                            <path d="M11.414 10l-7.383 7.418a2.091 2.091 0 0 0 0 2.967a2.11 2.11 0 0 0 2.976 0l7.407 -7.385"></path>
                            <path d="M18.121 15.293l2.586 -2.586a1 1 0 0 0 0 -1.414l-7.586 -7.586a1 1 0 0 0 -1.414 0l-2.586 2.586a1 1 0 0 0 0 1.414l7.586 7.586a1 1 0 0 0 1.414 0z"></path>
                        </svg>
                    </h1>
                </div>
                <div class="card-body">
                    <p class="card-text text-center">Em breve teremos novidades!
                    </p>
                    <p class="card-text text-center">
                        Continue navegando pelo sistema.
                    </p>
                    <div class="d-flex justify-content-center">
                        <a type="button" class="btn btn-secondary text-dark mx-2" href="/extrato">Extrato</a>
                        <a type="button" class="btn btn-secondary text-dark mx-2" href="/operar">Operar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
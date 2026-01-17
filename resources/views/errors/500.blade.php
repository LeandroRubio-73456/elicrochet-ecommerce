@extends('errors.minimal')

@section('title', __('Error del Servidor'))
@section('code', '500')
@section('message', __('Algo salió mal en nuestros servidores. Por favor intenta más tarde.'))

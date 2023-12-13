@extends('vStack::resources.field.template')
@if ($slot)
    @section('slot')
        {!! $slot !!}
    @endsection
@endif

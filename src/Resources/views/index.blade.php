@extends('todo-inspector::layouts.app')

@section('title', 'Dashboard')

@section('content')
    @include('todo-inspector::partials.stats')
    @include('todo-inspector::partials.filters')
    @include('todo-inspector::partials.bulk-actions')
    @include('todo-inspector::partials.table')
@endsection

@push('scripts')
    @include('todo-inspector::partials.scripts')
@endpush
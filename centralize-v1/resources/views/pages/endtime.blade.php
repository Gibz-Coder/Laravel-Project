@extends('layouts.pages.endtime')
@section('title', 'Endtime & Submitted')

@section('content')

@livewire('endtime-dashboard')

@endsection

@push('scripts')
<!-- APEX CHARTS JS -->
<script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>

<!-- LIVEWIRE ENDTIME JS -->
<script src="{{ asset('js/livewire-endtime.js') }}"></script>

<!-- ENDTIME BUTTONS JS -->
<script src="{{ asset('js/endtime-buttons.js') }}"></script>

<!-- ENDTIME ENTRY JS -->
<script src="{{ asset('js/endtime-entry.js') }}"></script>

<!-- MODAL FIX JS -->
<script src="{{ asset('js/modal-fix.js') }}"></script>
@endpush

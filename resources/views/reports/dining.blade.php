@extends('layouts.app')

@section('title', 'Dining Reports')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Dining Reports</h4>
      </div>
      <div class="card-body text-center py-5">
        <i class="icon-base ri ri-history-line" style="font-size: 4rem; color: #6c757d;"></i>
        <h4 class="mt-3">Dining Reports Have Moved!</h4>
        <p class="text-muted">We've created a comprehensive dining history and analytics system.</p>
        <a href="{{ route('dining.history') }}" class="btn btn-primary">
          <i class="icon-base ri ri-arrow-right-line me-1"></i>
          Go to Dining History
        </a>
      </div>
    </div>
  </div>
</div>
@endsection 
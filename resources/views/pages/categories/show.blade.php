@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Category Details</h1>
    <p><strong>Name:</strong> {{ $category->name }}</p>
    <p><strong>Slug:</strong> {{ $category->slug }}</p>
    <p><strong>Active:</strong> {{ $category->is_active ? 'Yes' : 'No' }}</p>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection

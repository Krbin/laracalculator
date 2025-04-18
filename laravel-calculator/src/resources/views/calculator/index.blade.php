{{-- resources/views/calculator/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Advanced Calculator</div>

                    <div class="card-body">
                        <form action="{{ route('calculator.calculate') }}" method="GET">
                            <div class="mb-4">
                                <label for="expression" class="form-label">Enter Mathematical Expression</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="expression" name="expression"
                                        value="{{ old('expression', $expression ?? '') }}" placeholder="e.g., (2+3)*4-5^2">
                                    <button type="submit" class="btn btn-primary">Calculate</button>
                                    <a href="{{ route('calculator.index') }}" class="btn btn-secondary">Clear</a>
                                </div>
                                <small class="form-text text-muted">
                                    Supports: +, -, *, /, ^ (power), parentheses (), and functions <code>sin()</code>,
                                    <code>cos()</code>, <code>tan()</code>, <code>ln()</code>, <code>log()</code>
                                </small>
                            </div>
                        </form>


                        @if ($errors->any())
                            <div class="mb-4">
                                <div class="alert alert-danger" role="alert">
                                    <strong>Error:</strong> {{ $errors->first('expression') }}
                                </div>
                            </div>
                        @endif

                        @if (isset($result))
                            <div class="mb-4">
                                <div class="card">
                                    <div class="card-header">Result</div>
                                    <div class="card-body">
                                        <h2 class="text-center">{{ $result }}</h2>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (isset($expressionTree))
                            <div class="mb-4">
                                <div class="card">
                                    <div class="card-header">Expression Tree</div>
                                    <div class="card-body">
                                        <pre class="bg-light p-3 rounded">{{ json_encode($expressionTree, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- No JS needed when only using keyboard input --}}
@endsection

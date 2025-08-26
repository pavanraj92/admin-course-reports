@extends('admin::admin.layouts.master')

@section('title', 'Report Manager')
@section('page-title', 'Report Manager')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Report Manager</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-body">
                <h4 class="card-title">Filter</h4>
                <form action="{{ route('admin.reports.index') }}" method="GET" id="filterForm">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="text" name="start_date" id="start_date" value="{{ $startDate }}" class="form-control" placeholder="Select start date">

                                {{-- <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ app('request')->query('start_date') }}"> --}}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="text" name="end_date" id="end_date" value="{{ $endDate }}" class="form-control" placeholder="Select end date">

                                {{-- <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="{{ app('request')->query('end_date') }}"> --}}
                            </div>
                        </div>
                        <div class="col-auto mt-1 text-right">
                            <div class="form-group">
                                <button type="submit" form="filterForm" class="btn btn-primary mt-4">Filter</button>
                                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary mt-4">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transactions -->
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-header bg-primary text-white">Transactions</div>
                <div class="card-body">
                    <h4>Total Transactions: {{ $transactionCount }}</h4>
                    <p>Total Revenue: <strong>${{ number_format($transactionTotal, 2) }}</strong></p>
                </div>
            </div>
        </div>

        <!-- Purchases -->
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-header bg-success text-white">Course Purchases</div>
                <div class="card-body">
                    <h4>Total Purchases: {{ $purchaseCount }}</h4>
                    <p>Total Revenue: <strong>${{ number_format($purchaseRevenue, 2) }}</strong></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="card mt-4">
        <div class="card-header bg-info text-white">Purchase Status Breakdown</div>
        <div class="card-body">
            @foreach($purchaseByStatus as $status => $count)
            <span class="badge bg-secondary me-2 text-white">
                {{ ucfirst($status) }}: {{ $count }}
            </span>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endpush

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

<script>
    $(function() {
        // Apply datepicker
        $("#start_date, #end_date").datepicker({
            dateFormat: "yy-mm-dd", // Matches your format 2025-08-11
            changeMonth: true,
            changeYear: true
        });

        // Optional: make end date always after start date
        $("#start_date").on("change", function() {
            var startDate = $(this).datepicker("getDate");
            $("#end_date").datepicker("option", "minDate", startDate);
        });
    });
</script>
@endpush
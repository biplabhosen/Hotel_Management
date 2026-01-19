@extends('layout.erp.app')

{{-- @section('title', 'Create Hotel') --}}

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Create Hotel</h4>
            <small class="text-muted">Register a new property in the system</small>
        </div>
        <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary">
            Back to List
        </a>
    </div>

    <form action="{{ route('hotels.store') }}" method="POST">
        @csrf

        <div class="row">

            {{-- Left Column --}}
            <div class="col-lg-8">

                {{-- Basic Information --}}
                <div class="card mb-4">
                    <div class="card-header fw-semibold">
                        Basic Information
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Hotel Name *</label>
                                <input type="text" name="name"
                                       class="form-control"
                                       placeholder="Grand Palace Hotel"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Hotel Code</label>
                                <input type="text" name="code"
                                       class="form-control"
                                       placeholder="GP-DHK">
                                <small class="text-muted">Internal reference (optional)</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email"
                                       class="form-control"
                                       placeholder="info@hotel.com">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone"
                                       class="form-control"
                                       placeholder="+880 1XXXXXXXXX">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address"
                                          class="form-control"
                                          rows="2"
                                          placeholder="Street, City, Country"></textarea>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Operational Settings --}}
                <div class="card mb-4">
                    <div class="card-header fw-semibold">
                        Operational Settings
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label">Check-in Time</label>
                                <input type="time" name="check_in_time"
                                       class="form-control"
                                       value="14:00">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Check-out Time</label>
                                <input type="time" name="check_out_time"
                                       class="form-control"
                                       value="12:00">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Timezone</label>
                                <select name="timezone" class="form-select">
                                    <option value="Asia/Dhaka">Asia/Dhaka</option>
                                    <option value="UTC">UTC</option>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column --}}
            <div class="col-lg-4">

                {{-- Financial & Compliance --}}
                <div class="card mb-4">
                    <div class="card-header fw-semibold">
                        Financial Settings
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label">Currency</label>
                            <select name="currency" class="form-select">
                                <option value="BDT">BDT – Bangladeshi Taka</option>
                                <option value="USD">USD – US Dollar</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tax Percentage (%)</label>
                            <input type="number"
                                   step="0.01"
                                   name="tax_percentage"
                                   class="form-control"
                                   placeholder="15">
                        </div>

                    </div>
                </div>

                {{-- Status --}}
                <div class="card mb-4">
                    <div class="card-header fw-semibold">
                        Status
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="status"
                                   value="active"
                                   checked>
                            <label class="form-check-label">
                                Active Hotel
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        Create Hotel
                    </button>
                    <a href="{{ route('hotels.index') }}"
                       class="btn btn-light">
                        Cancel
                    </a>
                </div>

            </div>
        </div>

    </form>

</div>
@endsection


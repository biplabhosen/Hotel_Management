@extends('layout.erp.app')

@section('content')

    <style>
        body {
            background-color: #f4f6f9;
            font-size: 14px;
        }
        .card {
            border-radius: 10px;
        }
        .form-control, .form-select {
            height: 42px;
        }
        textarea.form-control {
            height: auto;
        }
        .upload-box {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            background-color: #fafafa;
        }
        .upload-box input {
            display: none;
        }
        .upload-box label {
            cursor: pointer;
            color: #6f42c1;
            font-weight: 500;
        }
        .required::after {
            content: " *";
            color: red;
        }
    </style>

<body>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">

            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    Add Booking
                </div>

                <div class="card-body">
                    <form>
                        <div class="row g-3">

                            <!-- Guest Info -->
                            <div class="col-md-6">
                                <label class="form-label required">First Name</label>
                                <input type="text" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Email</label>
                                <input type="email" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Gender</label>
                                <select class="form-select">
                                    <option selected disabled>Select gender</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                    <option>Other</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Mobile</label>
                                <input type="text" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">City</label>
                                <input type="text" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">ID / Passport Number</label>
                                <input type="text" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Nationality</label>
                                <input type="text" class="form-control">
                            </div>

                            <!-- Booking Info -->
                            <div class="col-md-6">
                                <label class="form-label required">Check-In & Check-Out</label>
                                <input type="date" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Package Type</label>
                                <select class="form-select">
                                    <option>Select package</option>
                                    <option>Standard</option>
                                    <option>Premium</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Total Person</label>
                                <input type="number" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Number of Rooms</label>
                                <input type="number" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Room Type</label>
                                <select class="form-select">
                                    <option>Select room type</option>
                                    <option>Single</option>
                                    <option>Double</option>
                                    <option>Suite</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Arrival Time</label>
                                <input type="time" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Purpose of Stay</label>
                                <select class="form-select">
                                    <option>Business</option>
                                    <option>Tourism</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Payment Method</label>
                                <select class="form-select">
                                    <option>Cash</option>
                                    <option>Card</option>
                                    <option>Mobile Banking</option>
                                </select>
                            </div>

                            <!-- Emergency -->
                            <div class="col-md-6">
                                <label class="form-label required">Emergency Contact Name</label>
                                <input type="text" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Emergency Contact Phone</label>
                                <input type="text" class="form-control">
                            </div>

                            <!-- Extra -->
                            <div class="col-md-6">
                                <label class="form-label">Discount Code</label>
                                <input type="text" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Booking Reference</label>
                                <input type="text" class="form-control" value="BK691086CLLWR1" readonly>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" rows="2"></textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Special Requests</label>
                                <textarea class="form-control" rows="2"></textarea>
                            </div>

                            <!-- Upload -->
                            <div class="col-md-12">
                                <label class="form-label">Upload</label>
                                <div class="upload-box">
                                    <input type="file" id="upload">
                                    <label for="upload">Choose file</label>
                                    <span class="text-muted"> or drag & drop here</span>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Note</label>
                                <textarea class="form-control" rows="2"></textarea>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-12 text-end mt-3">
                                <button type="submit" class="btn btn-primary px-4">Submit</button>
                                <button type="button" class="btn btn-danger px-4 ms-2">Cancel</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
@endsection

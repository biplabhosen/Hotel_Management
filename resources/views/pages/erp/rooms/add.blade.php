@extends('layout.erp.app')

@section('content')
    <style>
        body {
            background: #f4f7fb;
        }

        /* Card */
        .card-form {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
        }

        /* Floating label */
        .form-group {
            position: relative;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            height: 50px;
            padding: 14px 12px;
        }

        .form-group textarea {
            height: auto;
        }

        .form-group label {
            position: absolute;
            top: 50%;
            left: 12px;
            background: #fff;
            padding: 0 6px;
            font-size: 14px;
            color: #6c757d;
            transform: translateY(-50%);
            pointer-events: none;
            transition: 0.2s ease;
        }

        .form-group input:focus+label,
        .form-group input:not(:placeholder-shown)+label,
        .form-group select:focus+label,
        .form-group select:valid+label,
        .form-group textarea:focus+label,
        .form-group textarea:not(:placeholder-shown)+label {
            top: -6px;
            font-size: 12px;
            color: #6c7ae0;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #6c7ae0;
            box-shadow: none;
        }

        /* Upload */
        .upload-box {
            border: 2px dashed #cfd6e0;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background: #fafbfe;
        }

        .upload-box input {
            display: none;
        }

        .upload-box label {
            cursor: pointer;
            color: #6c7ae0;
            font-weight: 500;
        }
    </style>
    {{-- <div class="container-fluid p-4"> --}}
        <div class="card-form">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <h5 class="mb-4">Add Room</h5>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('room/store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">

                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" name="room_number" class="form-control" value="{{ old('room_number') }}"
                                placeholder=" " required>
                            <label>Room No *</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <select name="room_type_id" class="form-select" required>
                                <option value="" disabled selected hidden></option>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <label>Select Room Type *</label>
                        </div>
                    </div>

                    {{-- <div class="col-md-6">
                        <div class="form-group">
                            <select name="ac_type" class="form-select" required>
                                <option value="" disabled selected hidden></option>
                                <option value="1">AC</option>
                                <option value="2">Non AC</option>
                            </select>
                            <label>AC / Non AC *</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <select class="form-select" required>
                                <option value="" disabled selected hidden></option>
                                <option>Breakfast</option>
                                <option>Half Board</option>
                                <option>Full Board</option>
                            </select>
                            <label>Select Meal *</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="number" name="capacity" value="{{ old('capacity') }}" class="form-control"
                                placeholder=" " required>
                            <label>Capacity *</label>
                        </div>
                    </div> --}}

                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="number" name="floor" class="form-control" placeholder=" " required>
                            <label>Floor Number *</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <select name="status" class="form-select" required>
                                <option value="" disabled selected hidden></option>
                                <option value="available">Available</option>
                                <option value="booked">Occupied</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                            <label>Status *</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <select name="bed_type" class="form-select" required>
                                <option value="" disabled selected hidden></option>
                                <option value="Single">Single</option>
                                <option value="Double">Double</option>
                                <option value="King">King</option>
                            </select>
                            <label>Bed Type *</label>
                        </div>
                    </div>

                    {{-- <div class="col-md-6">
                        <div class="form-group">
                            <input type="number" name="price_per_night" class="form-control" placeholder=" ">
                            <label>Rent per Night</label>
                        </div>
                    </div> --}}

                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="number" name="bed_count" class="form-control" placeholder=" " required>
                            <label>Number of Beds *</label>
                        </div>
                    </div>

                    {{-- <div class="col-md-6">
                        <div class="form-group">
                            <input type="number" class="form-control" placeholder=" " required>
                            <label>Room Size (sq ft) *</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <select class="form-select" required>
                                <option value="" disabled selected hidden></option>
                                <option>City View</option>
                                <option>Sea View</option>
                            </select>
                            <label>View Type *</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" name="phone" class="form-control " placeholder=" " required>
                            <label>Contact Mobile *</label>
                            <div class="invalid-feedback">Mobile is required</div>
                        </div>
                    </div>

                    <!-- Upload -->
                    <div class="col-12">
                        <div class="upload-box">
                            <label>
                                <i data-feather="upload"></i> Choose file
                                <input type="file">
                            </label>
                            <p class="text-muted mt-2">or drag and drop file here</p>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <textarea class="form-control" rows="3" placeholder=" "></textarea>
                            <label>Note</label>
                        </div>
                    </div> --}}

                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    {{-- <button class="btn btn-danger">Cancel</button> --}}
                </div>

            </form>

        </div>
    </div>
@endsection

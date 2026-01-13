@extends('layout.erp.app')

@section('content')
    <div class="content">
        <div class="content-page-header content-page-headersplit">
            <h5>Booking List</h5>
            <div class="list-btn">
                <ul>
                    <li>
                        <div class="filter-sorting">
                            <ul>
                                <li>
                                    <a href="javascript:void(0);" class="filter-sets"><i
                                            class="fe fe-filter me-2"></i>Filter</a>
                                </li>
                                <li>
                                    <span><img src="assets/img/icons/sort.svg" class="me-2" alt="img"></span>
                                    <div class="review-sort">
                                        <select class="select">
                                            <option>A -> Z</option>
                                            <option>Z -> A</option>
                                        </select>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="tab-sets">
                    <div class="tab-contents-sets">
                        <ul>
                            <li>
                                <a href="booking.html" class="active">All Booking</a>
                            </li>
                            <li>
                                <a href="pending-booking.html">Pending </a>
                            </li>
                            <li>
                                <a href="inprogress-booking.html">Inprogress </a>
                            </li>
                            <li>
                                <a href="completed-booking.html">Completed </a>
                            </li>
                            <li>
                                <a href="cancelled-booking.html">Cancelled</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-contents-count">
                        <h6>Showing 8-10 of 84 results</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 ">
                <div class="table-resposnive table-div">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Booking Time</th>
                                <th>Provider</th>
                                <th>User</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>28 Sep 2023</td>
                                <td>10:00:00 - 11:00:00</td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-01.jpg" class="me-2" alt="img">
                                        <span>John Smith</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-03.jpg" class="me-2" alt="img">
                                        <span>Sharon</span>

                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-imgname">
                                        <img src="assets/img/services/service-03.jpg" class="me-2" alt="img">
                                        <span>Computer Repair</span>
                                    </a>
                                </td>
                                <td>$80</td>
                                <td>
                                    <h6 class="badge-pending">Pending</h6>
                                </td>
                                <td>07 Oct 2023 11:22:51 </td>
                                <td>
                                    <div class="table-select">
                                        <div class="form-group mb-0">
                                            <select class="select">
                                                <option>Select Status</option>
                                                <option> Pending</option>
                                                <option> Inprogress</option>
                                                <option>Completed</option>
                                                <option>cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>10 Sep 2023</td>
                                <td>18:00:00 - 19:00:00 </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-04.jpg" class="me-2" alt="img">
                                        <span>Johnny</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-05.jpg" class="me-2" alt="img">
                                        <span>Pricilla</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-imgname">
                                        <img src="assets/img/services/service-02.jpg" class="me-2" alt="img">
                                        <span>Car Repair Services</span>
                                    </a>
                                </td>
                                <td>$50</td>
                                <td>
                                    <h6 class="badge-active">Completed</h6>
                                </td>
                                <td>07 Oct 2023 11:22:51</td>
                                <td>
                                    <div class="table-select">
                                        <div class="form-group mb-0">
                                            <select class="select">
                                                <option>Select Status</option>
                                                <option> Pending</option>
                                                <option> Inprogress</option>
                                                <option>Completed</option>
                                                <option>cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>25 Sep 2023</td>
                                <td>12:00:00 - 13:00:00</td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-06.jpg" class="me-2" alt="img">
                                        <span>Robert</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-02.jpg" class="me-2" alt="img">
                                        <span>Amanda</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-imgname">
                                        <img src="assets/img/services/service-04.jpg" class="me-2" alt="img">
                                        <span>Steam Car Wash</span>
                                    </a>
                                </td>
                                <td>$50</td>
                                <td>
                                    <h6 class="badge-inactive">Inprogress</h6>
                                </td>
                                <td>07 Oct 2023 11:22:51</td>
                                <td>
                                    <div class="table-select">
                                        <div class="form-group mb-0">
                                            <select class="select">
                                                <option>Select Status</option>
                                                <option> Pending</option>
                                                <option> Inprogress</option>
                                                <option>Completed</option>
                                                <option>cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>08 Sep 2023</td>
                                <td>07 Oct 2023 11:22:51</td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-09.jpg" class="me-2" alt="img">
                                        <span>Sharonda</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-01.jpg" class="me-2" alt="img">
                                        <span>James</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-imgname">
                                        <img src="assets/img/services/service-09.jpg" class="me-2" alt="img">
                                        <span>House Cleaning </span>
                                    </a>
                                </td>
                                <td>$50</td>
                                <td>
                                    <h6 class="badge-delete">Cancelled</h6>
                                </td>
                                <td>07 Oct 2023 11:22:51</td>
                                <td>
                                    <div class="table-select">
                                        <div class="form-group mb-0">
                                            <select class="select">
                                                <option>Select Status</option>
                                                <option> Pending</option>
                                                <option> Inprogress</option>
                                                <option>Completed</option>
                                                <option>cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>28 Sep 2023</td>
                                <td>10:00:00 - 11:00:00</td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-01.jpg" class="me-2" alt="img">
                                        <span>John Smith</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-03.jpg" class="me-2" alt="img">
                                        <span>Sharon</span>

                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-imgname">
                                        <img src="assets/img/services/service-03.jpg" class="me-2" alt="img">
                                        <span>Computer Repair</span>
                                    </a>
                                </td>
                                <td>$80</td>
                                <td>
                                    <h6 class="badge-pending">Pending</h6>
                                </td>
                                <td>07 Oct 2023 11:22:51 </td>
                                <td>
                                    <div class="table-select">
                                        <div class="form-group mb-0">
                                            <select class="select">
                                                <option>Select Status</option>
                                                <option> Pending</option>
                                                <option> Inprogress</option>
                                                <option>Completed</option>
                                                <option>cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>10 Sep 2023</td>
                                <td>18:00:00 - 19:00:00 </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-04.jpg" class="me-2" alt="img">
                                        <span>Johnny</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-05.jpg" class="me-2" alt="img">
                                        <span>Pricilla</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-imgname">
                                        <img src="assets/img/services/service-02.jpg" class="me-2" alt="img">
                                        <span>Car Repair Services</span>
                                    </a>
                                </td>
                                <td>$50</td>
                                <td>
                                    <h6 class="badge-active">Completed</h6>
                                </td>
                                <td>07 Oct 2023 11:22:51</td>
                                <td>
                                    <div class="table-select">
                                        <div class="form-group mb-0">
                                            <select class="select">
                                                <option>Select Status</option>
                                                <option> Pending</option>
                                                <option> Inprogress</option>
                                                <option>Completed</option>
                                                <option>cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>25 Sep 2023</td>
                                <td>12:00:00 - 13:00:00</td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-06.jpg" class="me-2" alt="img">
                                        <span>Robert</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-02.jpg" class="me-2" alt="img">
                                        <span>Amanda</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-imgname">
                                        <img src="assets/img/services/service-04.jpg" class="me-2" alt="img">
                                        <span>Steam Car Wash</span>
                                    </a>
                                </td>
                                <td>$50</td>
                                <td>
                                    <h6 class="badge-inactive">Inprogress</h6>
                                </td>
                                <td>07 Oct 2023 11:22:51</td>
                                <td>
                                    <div class="table-select">
                                        <div class="form-group mb-0">
                                            <select class="select">
                                                <option>Select Status</option>
                                                <option> Pending</option>
                                                <option> Inprogress</option>
                                                <option>Completed</option>
                                                <option>cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td>08 Sep 2023</td>
                                <td>07 Oct 2023 11:22:51</td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-09.jpg" class="me-2" alt="img">
                                        <span>Sharonda</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-profileimage">
                                        <img src="assets/img/customer/user-01.jpg" class="me-2" alt="img">
                                        <span>James</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="table-imgname">
                                        <img src="assets/img/services/service-09.jpg" class="me-2" alt="img">
                                        <span>House Cleaning </span>
                                    </a>
                                </td>
                                <td>$50</td>
                                <td>
                                    <h6 class="badge-delete">Cancelled</h6>
                                </td>
                                <td>07 Oct 2023 11:22:51</td>
                                <td>
                                    <div class="table-select">
                                        <div class="form-group mb-0">
                                            <select class="select">
                                                <option>Select Status</option>
                                                <option> Pending</option>
                                                <option> Inprogress</option>
                                                <option>Completed</option>
                                                <option>cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

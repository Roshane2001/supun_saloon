<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <span class="fw-mediumbold"> Add</span>
                    <span class="fw-light"> New Customer </span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCustomerForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group form-group-default">
                                <label for="new_customer_name">Customer Name</label>
                                <input type="text" class="form-control" id="new_customer_name" name="customer_name" placeholder="Enter Customer Name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label for="new_contact_no">Contact No</label>
                                <input type="text" class="form-control" id="new_contact_no" name="contact_no" placeholder="Enter Contact Number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label for="new_email">Email Address</label>
                                <input type="email" class="form-control" id="new_email" name="email" placeholder="Enter Email Address">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group form-group-default">
                                <label for="new_address">Address</label>
                                <textarea class="form-control" id="new_address" name="address" rows="3" placeholder="Enter Address"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary">Save Customer</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
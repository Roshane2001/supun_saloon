<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <span class="fw-mediumbold"> Edit</span>
                    <span class="fw-light"> Supplier </span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="suplier_update.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="supplier_id" id="edit_supplier_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Supplier Company Name</label>
                                <input id="edit_suplier_name" name="suplier_name" type="text" class="form-control" placeholder="Company Name" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Contact Person</label>
                                <input id="edit_contact_person" name="contact_person" type="text" class="form-control" placeholder="Contact Person" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Telephone No</label>
                                <input id="edit_tel_no" name="tel_no" type="text" class="form-control" placeholder="Telephone Number" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Email Address</label>
                                <input id="edit_email" name="email" type="email" class="form-control" placeholder="Email Address" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group form-group-default">
                                <label>Address</label>
                                <textarea id="edit_address" name="address" class="form-control" rows="3" placeholder="Enter Address"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" name="update_suplier" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

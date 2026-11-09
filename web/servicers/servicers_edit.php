<!-- Edit Servicer Modal (Used as a component in servicers_list.php) -->
<div class="modal fade" id="editServicerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <span class="fw-mediumbold"> Edit</span>
                    <span class="fw-light"> Servicer </span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="servicer_update.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="servicer_id" id="edit_servicer_id">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group form-group-default">
                                <label>Servicer Name</label>
                                <input id="edit_servicer_name" name="servicer_name" type="text" class="form-control" placeholder="Servicer Name" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Price</label>
                                <input id="edit_nic_no" name="nic_no" type="text" class="form-control" placeholder="Price" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Service Time</label>
                                <input id="edit_service_time" name="service_time" type="text" class="form-control" placeholder="Service Time" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Status</label>
                                <input id="edit_status" name="status" type="text" class="form-control" placeholder="Status" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" name="update_servicer" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
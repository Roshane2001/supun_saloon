<!-- Edit Staff Modal (Used as a component in staff_list.php) -->
<div class="modal fade" id="editStaffModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <span class="fw-mediumbold"> Edit</span>
                    <span class="fw-light"> Staff </span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="staff_update.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="staff_id" id="edit_staff_id">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group form-group-default">
                                <label>Staff Name</label>
                                <input id="edit_staff_name" name="staff_name" type="text" class="form-control" placeholder="Staff Name" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>NIC No.</label>
                                <input id="edit_nic_no" name="nic_no" type="text" class="form-control" placeholder="NIC Number" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Telephone</label>
                                <input id="edit_telephone_no" name="telephone_no" type="tel" class="form-control" placeholder="Telephone Number (10 digits)"
                                    inputmode="numeric" pattern="0[0-9]{9}" maxlength="10"
                                    title="Enter a 10-digit number starting with 0, e.g. 0771234567"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>User Name</label>
                                <input id="edit_username" name="username" type="text" class="form-control" placeholder="User Name" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Password (Leave blank to keep current)</label>
                                <div class="position-relative">
                                    <input id="edit_password" name="password" type="password" class="form-control" placeholder="New Password" />
                                    <i class="show-password fa fa-eye"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group form-group-default">
                                <label>Address</label>
                                <textarea id="edit_address" name="address" class="form-control" rows="3" placeholder="Address"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group form-group-default">
                                <label>Role</label>
                                <select class="form-select" id="edit_role_id" name="role_id" required>
                                    <option value="">Select Role</option>
                                    <?php 
                                    $roles_query = mysqli_query($con, "SELECT * FROM tblrole");
                                    while($role = mysqli_fetch_assoc($roles_query)): 
                                    ?>
                                    <option value="<?php echo $role['id']; ?>"><?php echo ucwords($role['role']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" name="update_staff" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Appointment Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <span class="fw-mediumbold"> Edit</span>
                    <span class="fw-light"> Appointment </span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="appointment_update.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="appointment_id" id="edit_appointment_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Service</label>
                                <select class="form-select" id="edit_service_id" name="service_id" required>
                                    <option value="">Select Service</option>
                                    <?php 
                                    $parents_query = mysqli_query($con, "SELECT * FROM tblappointment GROUP BY service_id");
                                    while($parent = mysqli_fetch_assoc($parents_query)): 
                                    ?>
                                    <option value="<?php echo $parent['id']; ?>">
                                        <?php echo htmlspecialchars($parent['service_name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group form-group-default">
                                <label>Customer Name</label>
                                <input id="edit_customer_name" name="customer_name" type="text" class="form-control"
                                    placeholder="Customer Name" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Date</label>
                                <input id="edit_date" name="date" type="date" class="form-control"
                                    placeholder="Date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Time</label>
                                <input id="edit_time" name="time" type="time" class="form-control"
                                    placeholder="Time">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Status</label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="0">Cancel</option>
                                    <option value="1">Pending</option>
                                    <option value="2">Confirm</option>
                                    <option value="3">Complete</option>
                                </select>
                            </div>
                        </div>  
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" name="update_product" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle parent category change inside the edit modal to refresh sub-categories
    $('#edit_parent_category_id').change(function() {
        var parentId = $(this).val();
        // Retrieve the stored subId if this change was triggered by the edit button click
        var subId = $('#editProductModal').data('selected-sub');

        if (parentId) {
            $.ajax({
                type: 'POST',
                url: 'get_sub_categories.php',
                data: {
                    parent_id: parentId
                },
                success: function(html) {
                    $('#edit_sub_category_id').html(html);
                    if (typeof subId !== 'undefined' && subId !== null) {
                        $('#edit_sub_category_id').val(subId);
                        $('#editProductModal').removeData(
                        'selected-sub'); // Clear after selection
                    }
                }
            });
        } else {
            $('#edit_sub_category_id').html('<option value="">Select Sub Category</option>');
            $('#editProductModal').removeData('selected-sub');
        }
    });

    // Re-trigger the sub-category load when the edit button is clicked
    $(document).on('click', '.btn-edit-product', function() {
        var btn = $(this);
        var parentId = btn.data('parent');
        var subId = btn.data('sub');
        var suplierId = btn.data('suplier');

        // Populate standard fields
        $('#edit_appointment_id').val(btn.data('id'));
        $('#edit_parent_category_id').val(parentId);
        $('#edit_product_name').val(btn.data('name'));
        $('#edit_product_code').val(btn.data('code'));
        $('#edit_price').val(btn.data('price'));
        $('#edit_suplier_id').val(suplierId);

        // Store subId temporarily in the modal and trigger change to fetch options via AJAX
        $('#editProductModal').data('selected-sub', subId);
        $('#edit_parent_category_id').trigger('change');
    });
});
</script>
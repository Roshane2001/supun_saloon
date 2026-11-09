<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <span class="fw-mediumbold"> Edit</span>
                    <span class="fw-light"> Category </span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="category_list_edit_update.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="category_id" id="edit_category_id">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group form-group-default">
                                <label>Parent Category</label>
                                <select class="form-select" id="edit_parent_category_id" name="parent_category_id" required>
                                    <option value="">Select Parent Category</option>
                                    <?php 
                                    $parents_query = mysqli_query($con, "SELECT * FROM tblparent_category ORDER BY parent_category_name ASC");
                                    while($parent = mysqli_fetch_assoc($parents_query)): 
                                    ?>
                                    <option value="<?php echo $parent['id']; ?>"><?php echo htmlspecialchars($parent['parent_category_name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group form-group-default">
                                <label>Sub Category Name</label>
                                <input id="edit_sub_category_name" name="sub_category_name" type="text" class="form-control" placeholder="Sub Category Name" required />
                            </div>
                        </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" name="update_category" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
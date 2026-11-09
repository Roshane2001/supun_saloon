<!-- Add Parent Category Modal -->
<div class="modal fade" id="addParentCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <span class="fw-mediumbold"> New</span>
                    <span class="fw-light"> Parent Category </span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="parent_category_submit.php" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group form-group-default">
                                <label>Category Name</label>
                                <input id="parent_category_name" name="parent_category_name" type="text" class="form-control" placeholder="Enter Parent Category Name" required />
                            </div>
                        </div>                       
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" name="save_parent_category" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
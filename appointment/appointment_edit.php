
<!-- Edit Appointment Modal -->
<div class="modal fade" id="editAppointmentModal" tabindex="-1"
    role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <span class="fw-mediumbold">Edit</span>
                    <span class="fw-light">Appointment</span>
                </h5>

                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>

            <form action="appointment_update.php" method="POST">

                <div class="modal-body">

                    <input type="hidden" name="appointment_id"
                        id="edit_appointment_id">

                    <div class="row">

                        <!-- Service -->
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Service</label>

                                <select class="form-select"
                                    id="edit_service_id"
                                    name="service_id" required>

                                    <option value="">Select Service</option>

                                    <?php
                                    $services_query = mysqli_query(
                                        $con,
                                        "SELECT id, servicer_name
                                         FROM servicers
                                         WHERE status = 1
                                         ORDER BY servicer_name ASC"
                                    );

                                    while ($service = mysqli_fetch_assoc($services_query)):
                                    ?>

                                    <option value="<?php echo $service['id']; ?>">
                                        <?php echo htmlspecialchars($service['servicer_name']); ?>
                                    </option>

                                    <?php endwhile; ?>

                                </select>
                            </div>
                        </div>

                        <!-- Customer Name -->
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Customer Name</label>

                                <input type="text"
                                    class="form-control"
                                    name="customer_name"
                                    id="edit_customer_name"
                                    placeholder="Customer Name"
                                    required>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Date</label>

                                <input type="date"
                                    class="form-control"
                                    name="date"
                                    id="edit_date"
                                    required>
                            </div>
                        </div>

                        <!-- Time -->
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Time</label>

                                <input type="time"
                                    class="form-control"
                                    name="time"
                                    id="edit_time"
                                    required>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Status</label>

                                <select class="form-select"
                                    name="status"
                                    id="edit_status"
                                    required>

                                    <option value="0">Cancel</option>
                                    <option value="1">Pending</option>
                                    <option value="2">Confirm</option>
                                    <option value="3">Complete</option>

                                </select>
                            </div>
                        </div>

                        <!-- Note -->
                        <div class="col-md-12">
                            <div class="form-group form-group-default">
                                <label>Note</label>

                                <textarea class="form-control"
                                    name="note"
                                    id="edit_note"
                                    rows="3"
                                    placeholder="Appointment Note"></textarea>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0">

                    <button type="submit"
                        name="update_appointment"
                        class="btn btn-primary">
                        Update
                    </button>

                    <button type="button"
                        class="btn btn-danger"
                        data-bs-dismiss="modal">
                        Close
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

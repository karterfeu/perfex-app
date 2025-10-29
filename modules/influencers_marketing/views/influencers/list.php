<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>

                        <!-- Header -->
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="no-margin"><?php echo _l('im_influencers'); ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if (has_permission('influencers_marketing', '', 'create')) { ?>
                                    <a href="<?php echo admin_url('influencers_marketing/influencer_form'); ?>" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> <?php echo _l('im_new_influencer'); ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>

                        <hr class="hr-panel-heading" />

                        <!-- Filters -->
                        <div class="row mtop15">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter-status"><?php echo _l('im_status'); ?></label>
                                            <select id="filter-status" class="form-control selectpicker" data-live-search="true">
                                                <option value=""><?php echo _l('im_all'); ?></option>
                                                <option value="prospect"><?php echo _l('im_status_prospect'); ?></option>
                                                <option value="contacted"><?php echo _l('im_status_contacted'); ?></option>
                                                <option value="negotiating"><?php echo _l('im_status_negotiating'); ?></option>
                                                <option value="active"><?php echo _l('im_status_active'); ?></option>
                                                <option value="inactive"><?php echo _l('im_status_inactive'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter-category"><?php echo _l('im_category'); ?></label>
                                            <input type="text" id="filter-category" class="form-control" placeholder="<?php echo _l('im_category'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="filter-min-score"><?php echo _l('im_min_score'); ?></label>
                                            <input type="number" id="filter-min-score" class="form-control" min="0" max="100" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="filter-max-score"><?php echo _l('im_max_score'); ?></label>
                                            <input type="number" id="filter-max-score" class="form-control" min="0" max="100" placeholder="100">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="button" id="apply-filters" class="btn btn-info btn-block">
                                                <i class="fa fa-filter"></i> <?php echo _l('im_filter'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="row mtop15">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-influencers dt-table" data-order-col="5" data-order-type="desc">
                                        <thead>
                                            <tr>
                                                <th><?php echo _l('im_influencer'); ?></th>
                                                <th><?php echo _l('im_email'); ?></th>
                                                <th><?php echo _l('im_category'); ?></th>
                                                <th><?php echo _l('im_status'); ?></th>
                                                <th><?php echo _l('im_influence_score'); ?></th>
                                                <th><?php echo _l('im_created_at'); ?></th>
                                                <th><?php echo _l('options'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data will be loaded via DataTables -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(function() {
    'use strict';

    var table = $('.table-influencers').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": true,
        "order": [[5, "desc"]],
        "columnDefs": [
            {
                "targets": [6],
                "orderable": false
            }
        ],
        "language": {
            "url": "<?php echo base_url('assets/plugins/jquery-datatables/language/' . get_option('active_language') . '.json'); ?>"
        }
    });

    // Apply filters
    $('#apply-filters').on('click', function() {
        var status = $('#filter-status').val();
        var category = $('#filter-category').val();
        var minScore = $('#filter-min-score').val();
        var maxScore = $('#filter-max-score').val();

        var params = [];
        if (status) params.push('status=' + status);
        if (category) params.push('category=' + category);
        if (minScore) params.push('min_score=' + minScore);
        if (maxScore) params.push('max_score=' + maxScore);

        var url = window.location.pathname;
        if (params.length > 0) {
            url += '?' + params.join('&');
        }
        window.location.href = url;
    });
});
</script>

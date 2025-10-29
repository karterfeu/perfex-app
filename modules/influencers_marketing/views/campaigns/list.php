<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Header -->
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="no-margin"><?php echo _l('im_campaigns'); ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if (has_permission('influencers_marketing', '', 'create')) { ?>
                                    <a href="<?php echo admin_url('influencers_marketing/campaign_form'); ?>" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> <?php echo _l('im_new_campaign'); ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>

                        <hr class="hr-panel-heading" />

                        <!-- Table -->
                        <div class="row mtop15">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped dt-table">
                                        <thead>
                                            <tr>
                                                <th><?php echo _l('im_campaign_name'); ?></th>
                                                <th><?php echo _l('im_status'); ?></th>
                                                <th><?php echo _l('im_budget'); ?></th>
                                                <th><?php echo _l('im_start_date'); ?></th>
                                                <th><?php echo _l('im_end_date'); ?></th>
                                                <th><?php echo _l('options'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($campaigns)): ?>
                                                <?php foreach ($campaigns as $campaign): ?>
                                                    <tr>
                                                        <td>
                                                            <a href="<?php echo admin_url('influencers_marketing/campaign/' . $campaign['id']); ?>">
                                                                <?php echo e($campaign['name']); ?>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="label label-<?php
                                                                switch($campaign['status']) {
                                                                    case 'active': echo 'success'; break;
                                                                    case 'completed': echo 'info'; break;
                                                                    case 'draft': echo 'default'; break;
                                                                    default: echo 'warning';
                                                                }
                                                            ?>"><?php echo ucfirst($campaign['status']); ?></span>
                                                        </td>
                                                        <td><?php echo app_format_money($campaign['budget'], $campaign['currency']); ?></td>
                                                        <td><?php echo _d($campaign['start_date']); ?></td>
                                                        <td><?php echo _d($campaign['end_date']); ?></td>
                                                        <td>
                                                            <a href="<?php echo admin_url('influencers_marketing/campaign/' . $campaign['id']); ?>" class="btn btn-default btn-xs">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <?php if (has_permission('influencers_marketing', '', 'edit')) { ?>
                                                                <a href="<?php echo admin_url('influencers_marketing/campaign_form/' . $campaign['id']); ?>" class="btn btn-default btn-xs">
                                                                    <i class="fa fa-pencil"></i>
                                                                </a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center"><?php echo _l('im_no_results'); ?></td>
                                                </tr>
                                            <?php endif; ?>
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

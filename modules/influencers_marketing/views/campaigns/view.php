<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo e($campaign['name']); ?></h4>
                        <hr class="hr-panel-heading" />

                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="bold"><?php echo _l('im_campaign_description'); ?></h5>
                                <p><?php echo nl2br(e($campaign['description'] ?: 'Aucune description')); ?></p>

                                <h5 class="bold mtop25"><?php echo _l('im_brief'); ?></h5>
                                <p><?php echo nl2br(e($campaign['brief'] ?: 'Aucun brief')); ?></p>

                                <h5 class="bold mtop25"><?php echo _l('im_goals'); ?></h5>
                                <p><?php echo nl2br(e($campaign['goals'] ?: 'Aucun objectif défini')); ?></p>
                            </div>

                            <div class="col-md-4">
                                <div class="panel_s">
                                    <div class="panel-body" style="background: #f8f9fa;">
                                        <h5 class="bold"><?php echo _l('im_campaign_details'); ?></h5>
                                        <hr />

                                        <p><strong><?php echo _l('im_status'); ?>:</strong>
                                            <span class="label label-<?php
                                                switch($campaign['status']) {
                                                    case 'active': echo 'success'; break;
                                                    case 'completed': echo 'info'; break;
                                                    case 'draft': echo 'default'; break;
                                                    default: echo 'warning';
                                                }
                                            ?>"><?php echo ucfirst($campaign['status']); ?></span>
                                        </p>

                                        <p><strong><?php echo _l('im_budget'); ?>:</strong><br/>
                                            <?php echo app_format_money($campaign['budget'], $campaign['currency']); ?>
                                        </p>

                                        <p><strong><?php echo _l('im_actual_cost'); ?>:</strong><br/>
                                            <?php echo app_format_money($campaign['actual_cost'], $campaign['currency']); ?>
                                        </p>

                                        <p><strong><?php echo _l('im_start_date'); ?>:</strong><br/>
                                            <?php echo _d($campaign['start_date']); ?>
                                        </p>

                                        <p><strong><?php echo _l('im_end_date'); ?>:</strong><br/>
                                            <?php echo _d($campaign['end_date']); ?>
                                        </p>

                                        <?php if (isset($campaign['roi'])): ?>
                                            <hr />
                                            <h5 class="bold"><?php echo _l('im_roi'); ?></h5>
                                            <p><strong><?php echo _l('im_revenue_generated'); ?>:</strong><br/>
                                                <?php echo app_format_money($campaign['roi']['return'], $campaign['currency']); ?>
                                            </p>
                                            <p><strong>ROI:</strong><br/>
                                                <span style="color: <?php echo $campaign['roi']['roi_percentage'] >= 0 ? '#2ecc71' : '#e74c3c'; ?>; font-size: 18px; font-weight: bold;">
                                                    <?php echo number_format($campaign['roi']['roi_percentage'], 1); ?>%
                                                </span>
                                            </p>
                                        <?php endif; ?>

                                        <hr />

                                        <?php if (has_permission('influencers_marketing', '', 'edit')) { ?>
                                            <a href="<?php echo admin_url('influencers_marketing/campaign_form/' . $campaign['id']); ?>" class="btn btn-primary btn-block">
                                                <i class="fa fa-pencil"></i> <?php echo _l('im_edit'); ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Influencers in Campaign -->
                        <div class="row mtop25">
                            <div class="col-md-12">
                                <h5 class="bold"><?php echo _l('im_campaign_influencers'); ?></h5>
                                <hr />

                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('im_influencer'); ?></th>
                                            <th><?php echo _l('im_status'); ?></th>
                                            <th><?php echo _l('im_compensation_type'); ?></th>
                                            <th><?php echo _l('im_compensation_amount'); ?></th>
                                            <th><?php echo _l('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($campaign['influencers'])): ?>
                                            <?php foreach ($campaign['influencers'] as $inf): ?>
                                                <tr>
                                                    <td>
                                                        <a href="<?php echo admin_url('influencers_marketing/influencer/' . $inf['influencer_id']); ?>">
                                                            <?php echo e($inf['full_name']); ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo ucfirst($inf['status']); ?></td>
                                                    <td><?php echo ucfirst($inf['compensation_type']); ?></td>
                                                    <td><?php echo app_format_money($inf['compensation_amount'], $inf['currency']); ?></td>
                                                    <td>
                                                        <a href="<?php echo admin_url('influencers_marketing/influencer/' . $inf['influencer_id']); ?>" class="btn btn-xs btn-default">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center"><?php echo _l('im_no_results'); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Deliverables -->
                        <div class="row mtop25">
                            <div class="col-md-12">
                                <h5 class="bold"><?php echo _l('im_deliverables'); ?></h5>
                                <hr />

                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('im_deliverable_title'); ?></th>
                                            <th><?php echo _l('im_influencer'); ?></th>
                                            <th><?php echo _l('im_deliverable_type'); ?></th>
                                            <th><?php echo _l('im_due_date'); ?></th>
                                            <th><?php echo _l('im_deliverable_status'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($campaign['deliverables'])): ?>
                                            <?php foreach ($campaign['deliverables'] as $deliverable): ?>
                                                <tr>
                                                    <td><?php echo e($deliverable['title']); ?></td>
                                                    <td><?php echo e($deliverable['influencer_name']); ?></td>
                                                    <td><?php echo ucfirst($deliverable['type']); ?></td>
                                                    <td><?php echo _d($deliverable['due_date']); ?></td>
                                                    <td>
                                                        <span class="label label-<?php
                                                            switch($deliverable['status']) {
                                                                case 'published': echo 'success'; break;
                                                                case 'approved': echo 'info'; break;
                                                                case 'in_review': echo 'warning'; break;
                                                                default: echo 'default';
                                                            }
                                                        ?>"><?php echo ucfirst($deliverable['status']); ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center"><?php echo _l('im_no_results'); ?></td>
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

<?php init_tail(); ?>

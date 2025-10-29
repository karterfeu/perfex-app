<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="im-header">
                            <h1><?php echo _l('im_dashboard'); ?></h1>
                        </div>

                        <!-- Stats Cards -->
                        <div class="row mtop25">
                            <!-- Total Influencers -->
                            <div class="col-md-3 col-sm-6">
                                <div class="panel_s">
                                    <div class="panel-body" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; border-radius: 8px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;"><?php echo _l('im_total_influencers'); ?></p>
                                                <h2 style="margin: 10px 0; font-size: 32px; font-weight: 700;"><?php echo $influencer_stats['total_influencers']; ?></h2>
                                            </div>
                                            <div>
                                                <i class="fa fa-users" style="font-size: 48px; opacity: 0.3;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Average Score -->
                            <div class="col-md-3 col-sm-6">
                                <div class="panel_s">
                                    <div class="panel-body" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); color: white; border-radius: 8px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;"><?php echo _l('im_avg_score'); ?></p>
                                                <h2 style="margin: 10px 0; font-size: 32px; font-weight: 700;"><?php echo number_format($influencer_stats['avg_influence_score'], 1); ?></h2>
                                            </div>
                                            <div>
                                                <i class="fa fa-star" style="font-size: 48px; opacity: 0.3;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Campaigns -->
                            <div class="col-md-3 col-sm-6">
                                <div class="panel_s">
                                    <div class="panel-body" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white; border-radius: 8px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;"><?php echo _l('im_total_campaigns'); ?></p>
                                                <h2 style="margin: 10px 0; font-size: 32px; font-weight: 700;"><?php echo $campaign_stats['total_campaigns']; ?></h2>
                                            </div>
                                            <div>
                                                <i class="fa fa-bullhorn" style="font-size: 48px; opacity: 0.3;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Budget -->
                            <div class="col-md-3 col-sm-6">
                                <div class="panel_s">
                                    <div class="panel-body" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); color: white; border-radius: 8px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;"><?php echo _l('im_total_budget'); ?></p>
                                                <h2 style="margin: 10px 0; font-size: 32px; font-weight: 700;"><?php echo app_format_money($campaign_stats['total_budget'], get_base_currency()); ?></h2>
                                            </div>
                                            <div>
                                                <i class="fa fa-euro" style="font-size: 48px; opacity: 0.3;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Row -->
                        <div class="row mtop25">
                            <!-- Top Influencers -->
                            <div class="col-md-6">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4 class="bold"><?php echo _l('im_top_performers'); ?></h4>
                                        <hr />
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th><?php echo _l('im_influencer'); ?></th>
                                                    <th><?php echo _l('im_influence_score'); ?></th>
                                                    <th><?php echo _l('im_status'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($influencer_stats['top_influencers'])): ?>
                                                    <?php foreach ($influencer_stats['top_influencers'] as $influencer): ?>
                                                        <tr>
                                                            <td>
                                                                <a href="<?php echo admin_url('influencers_marketing/influencer/' . $influencer['id']); ?>">
                                                                    <?php echo e($influencer['firstname'] . ' ' . $influencer['lastname']); ?>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <span class="badge" style="background-color: <?php
                                                                    if ($influencer['influence_score'] >= 80) echo '#2ecc71';
                                                                    elseif ($influencer['influence_score'] >= 60) echo '#3498db';
                                                                    elseif ($influencer['influence_score'] >= 40) echo '#f39c12';
                                                                    else echo '#e74c3c';
                                                                ?>">
                                                                    <?php echo number_format($influencer['influence_score'], 1); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php echo ucfirst($influencer['status']); ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center"><?php echo _l('im_no_results'); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Influencers -->
                            <div class="col-md-6">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4 class="bold"><?php echo _l('im_new_last_30_days'); ?></h4>
                                        <hr />
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th><?php echo _l('im_influencer'); ?></th>
                                                    <th><?php echo _l('im_category'); ?></th>
                                                    <th><?php echo _l('im_created_at'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($recent_influencers)): ?>
                                                    <?php foreach ($recent_influencers as $influencer): ?>
                                                        <tr>
                                                            <td>
                                                                <a href="<?php echo admin_url('influencers_marketing/influencer/' . $influencer['id']); ?>">
                                                                    <?php echo e($influencer['firstname'] . ' ' . $influencer['lastname']); ?>
                                                                </a>
                                                            </td>
                                                            <td><?php echo e($influencer['category'] ?: '-'); ?></td>
                                                            <td><?php echo _dt($influencer['created_at']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center"><?php echo _l('im_no_results'); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Campaigns -->
                        <div class="row mtop25">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4 class="bold"><?php echo _l('im_active_campaigns'); ?></h4>
                                        <hr />
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th><?php echo _l('im_campaign_name'); ?></th>
                                                    <th><?php echo _l('im_budget'); ?></th>
                                                    <th><?php echo _l('im_start_date'); ?></th>
                                                    <th><?php echo _l('im_end_date'); ?></th>
                                                    <th><?php echo _l('im_status'); ?></th>
                                                    <th><?php echo _l('options'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($active_campaigns)): ?>
                                                    <?php foreach ($active_campaigns as $campaign): ?>
                                                        <tr>
                                                            <td>
                                                                <a href="<?php echo admin_url('influencers_marketing/campaign/' . $campaign['id']); ?>">
                                                                    <?php echo e($campaign['name']); ?>
                                                                </a>
                                                            </td>
                                                            <td><?php echo app_format_money($campaign['budget'], $campaign['currency']); ?></td>
                                                            <td><?php echo _d($campaign['start_date']); ?></td>
                                                            <td><?php echo _d($campaign['end_date']); ?></td>
                                                            <td>
                                                                <span class="label label-success"><?php echo ucfirst($campaign['status']); ?></span>
                                                            </td>
                                                            <td>
                                                                <a href="<?php echo admin_url('influencers_marketing/campaign/' . $campaign['id']); ?>" class="btn btn-default btn-xs">
                                                                    <i class="fa fa-eye"></i>
                                                                </a>
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

                        <!-- Quick Actions -->
                        <div class="row mtop25">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body text-center">
                                        <h4 class="bold"><?php echo _l('quick_actions'); ?></h4>
                                        <hr />
                                        <a href="<?php echo admin_url('influencers_marketing/influencer_form'); ?>" class="btn btn-primary mright10">
                                            <i class="fa fa-plus"></i> <?php echo _l('im_add_influencer'); ?>
                                        </a>
                                        <a href="<?php echo admin_url('influencers_marketing/campaign_form'); ?>" class="btn btn-success mright10">
                                            <i class="fa fa-bullhorn"></i> <?php echo _l('im_new_campaign'); ?>
                                        </a>
                                        <a href="<?php echo admin_url('influencers_marketing/analytics'); ?>" class="btn btn-info">
                                            <i class="fa fa-bar-chart"></i> <?php echo _l('im_analytics'); ?>
                                        </a>
                                    </div>
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

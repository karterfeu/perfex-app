<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo e($influencer['firstname'] . ' ' . $influencer['lastname']); ?>
                            <?php if ($influencer['is_verified']): ?>
                                <i class="fa fa-check-circle text-success" title="Vérifié"></i>
                            <?php endif; ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <div class="row">
                            <div class="col-md-3">
                                <!-- Profile Picture -->
                                <div class="text-center">
                                    <?php if ($influencer['profile_picture']): ?>
                                        <img src="<?php echo base_url($influencer['profile_picture']); ?>" class="img-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                    <?php else: ?>
                                        <div style="width: 150px; height: 150px; margin: 0 auto; border-radius: 50%; background: #3498db; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: bold;">
                                            <?php echo strtoupper(substr($influencer['firstname'], 0, 1) . substr($influencer['lastname'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>

                                    <h4 class="mtop15"><?php echo e($influencer['firstname'] . ' ' . $influencer['lastname']); ?></h4>
                                    <p class="text-muted"><?php echo e($influencer['category'] ?: 'Non spécifié'); ?></p>

                                    <!-- Influence Score -->
                                    <div class="mtop15">
                                        <h3 class="bold" style="color: <?php
                                            if ($influencer['influence_score'] >= 80) echo '#2ecc71';
                                            elseif ($influencer['influence_score'] >= 60) echo '#3498db';
                                            elseif ($influencer['influence_score'] >= 40) echo '#f39c12';
                                            else echo '#e74c3c';
                                        ?>">
                                            <?php echo number_format($influencer['influence_score'], 1); ?>/100
                                        </h3>
                                        <p class="text-muted"><?php echo _l('im_influence_score'); ?></p>
                                    </div>
                                </div>

                                <hr />

                                <!-- Contact Info -->
                                <div class="mtop15">
                                    <p><i class="fa fa-envelope"></i> <?php echo e($influencer['email'] ?: '-'); ?></p>
                                    <p><i class="fa fa-phone"></i> <?php echo e($influencer['phone'] ?: '-'); ?></p>
                                    <p><i class="fa fa-map-marker"></i> <?php echo e($influencer['location'] ?: '-'); ?></p>
                                    <?php if ($influencer['country']): ?>
                                        <p><i class="fa fa-globe"></i> <?php echo e($influencer['country']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <hr />

                                <!-- Actions -->
                                <div class="mtop15">
                                    <?php if (has_permission('influencers_marketing', '', 'edit')) { ?>
                                        <a href="<?php echo admin_url('influencers_marketing/influencer_form/' . $influencer['id']); ?>" class="btn btn-primary btn-block">
                                            <i class="fa fa-pencil"></i> <?php echo _l('im_edit'); ?>
                                        </a>
                                    <?php } ?>

                                    <?php if (!$influencer['customer_id'] && has_permission('influencers_marketing', '', 'edit')) { ?>
                                        <a href="<?php echo admin_url('influencers_marketing/convert_to_customer/' . $influencer['id']); ?>" class="btn btn-success btn-block mtop10">
                                            <i class="fa fa-user-plus"></i> <?php echo _l('im_convert_to_customer'); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <!-- Tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#overview" aria-controls="overview" role="tab" data-toggle="tab">
                                            <?php echo _l('im_influencer_info'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#social" aria-controls="social" role="tab" data-toggle="tab">
                                            <?php echo _l('im_social_accounts'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#campaigns" aria-controls="campaigns" role="tab" data-toggle="tab">
                                            <?php echo _l('im_campaigns'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#interactions" aria-controls="interactions" role="tab" data-toggle="tab">
                                            <?php echo _l('im_interactions'); ?>
                                        </a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content mtop15">
                                    <!-- Overview Tab -->
                                    <div role="tabpanel" class="tab-pane active" id="overview">
                                        <h5 class="bold"><?php echo _l('im_bio'); ?></h5>
                                        <p><?php echo nl2br(e($influencer['bio'] ?: 'Aucune biographie')); ?></p>

                                        <hr />

                                        <h5 class="bold"><?php echo _l('im_notes'); ?></h5>
                                        <p><?php echo nl2br(e($influencer['notes'] ?: 'Aucune note')); ?></p>

                                        <hr />

                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong><?php echo _l('im_status'); ?>:</strong> <?php echo ucfirst($influencer['status']); ?></p>
                                                <p><strong><?php echo _l('im_rating'); ?>:</strong> <?php echo str_repeat('⭐', $influencer['rating']); ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong><?php echo _l('im_audience_quality'); ?>:</strong> <?php echo ucfirst($influencer['audience_quality']); ?></p>
                                                <p><strong><?php echo _l('im_created_at'); ?>:</strong> <?php echo _dt($influencer['created_at']); ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Social Accounts Tab -->
                                    <div role="tabpanel" class="tab-pane" id="social">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th><?php echo _l('im_platform'); ?></th>
                                                    <th><?php echo _l('im_username'); ?></th>
                                                    <th><?php echo _l('im_followers_count'); ?></th>
                                                    <th><?php echo _l('im_engagement_rate'); ?></th>
                                                    <th><?php echo _l('options'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($influencer['social_accounts'])): ?>
                                                    <?php foreach ($influencer['social_accounts'] as $account): ?>
                                                        <tr>
                                                            <td>
                                                                <i class="fa fa-<?php echo strtolower($account['platform']); ?>"></i>
                                                                <?php echo ucfirst($account['platform']); ?>
                                                                <?php if ($account['is_verified']): ?>
                                                                    <i class="fa fa-check-circle text-success"></i>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?php echo e($account['username']); ?></td>
                                                            <td><?php echo number_format($account['followers_count']); ?></td>
                                                            <td><?php echo number_format($account['engagement_rate'], 2); ?>%</td>
                                                            <td>
                                                                <?php if ($account['profile_url']): ?>
                                                                    <a href="<?php echo e($account['profile_url']); ?>" target="_blank" class="btn btn-xs btn-default">
                                                                        <i class="fa fa-external-link"></i>
                                                                    </a>
                                                                <?php endif; ?>
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

                                    <!-- Campaigns Tab -->
                                    <div role="tabpanel" class="tab-pane" id="campaigns">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th><?php echo _l('im_campaign_name'); ?></th>
                                                    <th><?php echo _l('im_status'); ?></th>
                                                    <th><?php echo _l('im_start_date'); ?></th>
                                                    <th><?php echo _l('options'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($influencer_campaigns)): ?>
                                                    <?php foreach ($influencer_campaigns as $campaign): ?>
                                                        <tr>
                                                            <td><?php echo e($campaign['name']); ?></td>
                                                            <td><?php echo ucfirst($campaign['status']); ?></td>
                                                            <td><?php echo _d($campaign['start_date']); ?></td>
                                                            <td>
                                                                <a href="<?php echo admin_url('influencers_marketing/campaign/' . $campaign['id']); ?>" class="btn btn-xs btn-default">
                                                                    <i class="fa fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center"><?php echo _l('im_no_results'); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Interactions Tab -->
                                    <div role="tabpanel" class="tab-pane" id="interactions">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th><?php echo _l('im_interaction_type'); ?></th>
                                                    <th><?php echo _l('im_interaction_subject'); ?></th>
                                                    <th><?php echo _l('im_interaction_date'); ?></th>
                                                    <th><?php echo _l('im_interaction_description'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($influencer['recent_interactions'])): ?>
                                                    <?php foreach ($influencer['recent_interactions'] as $interaction): ?>
                                                        <tr>
                                                            <td>
                                                                <i class="fa fa-<?php
                                                                    switch($interaction['type']) {
                                                                        case 'email': echo 'envelope'; break;
                                                                        case 'call': echo 'phone'; break;
                                                                        case 'meeting': echo 'calendar'; break;
                                                                        default: echo 'comment';
                                                                    }
                                                                ?>"></i>
                                                                <?php echo ucfirst($interaction['type']); ?>
                                                            </td>
                                                            <td><?php echo e($interaction['subject']); ?></td>
                                                            <td><?php echo _dt($interaction['contact_date']); ?></td>
                                                            <td><?php echo e(substr($interaction['description'], 0, 100)); ?><?php echo strlen($interaction['description']) > 100 ? '...' : ''; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center"><?php echo _l('im_no_results'); ?></td>
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
</div>

<?php init_tail(); ?>

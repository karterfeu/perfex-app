<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('im_analytics'); ?></h4>
                        <hr class="hr-panel-heading" />

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i>
                                    <?php echo _l('im_analytics_overview'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Charts will go here -->
                        <div class="row mtop25">
                            <div class="col-md-6">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h5><i class="fa fa-line-chart"></i> <?php echo _l('im_followers_growth'); ?></h5>
                                        <canvas id="followers-growth-chart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h5><i class="fa fa-pie-chart"></i> <?php echo _l('im_platform_distribution'); ?></h5>
                                        <canvas id="platform-chart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mtop25">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h5><i class="fa fa-bar-chart"></i> <?php echo _l('im_engagement_analysis'); ?></h5>
                                        <canvas id="engagement-chart" height="100"></canvas>
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

<script>
$(function() {
    'use strict';

    // Initialize charts with sample data
    // You can replace this with real data from AJAX calls
});
</script>

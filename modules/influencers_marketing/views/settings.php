<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('settings'); ?></h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open(admin_url('influencers_marketing/settings')); ?>

                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="bold"><?php echo _l('im_general_settings'); ?></h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="im_default_currency"><?php echo _l('im_default_currency'); ?></label>
                                    <select name="im_default_currency" id="im_default_currency" class="form-control selectpicker">
                                        <option value="EUR" <?php echo (isset($settings['im_default_currency']) && $settings['im_default_currency'] == 'EUR') ? 'selected' : ''; ?>>EUR</option>
                                        <option value="USD" <?php echo (isset($settings['im_default_currency']) && $settings['im_default_currency'] == 'USD') ? 'selected' : ''; ?>>USD</option>
                                        <option value="GBP" <?php echo (isset($settings['im_default_currency']) && $settings['im_default_currency'] == 'GBP') ? 'selected' : ''; ?>>GBP</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="im_default_view"><?php echo _l('im_default_view'); ?></label>
                                    <select name="im_default_view" id="im_default_view" class="form-control selectpicker">
                                        <option value="grid" <?php echo (isset($settings['im_default_view']) && $settings['im_default_view'] == 'grid') ? 'selected' : ''; ?>>Grid</option>
                                        <option value="list" <?php echo (isset($settings['im_default_view']) && $settings['im_default_view'] == 'list') ? 'selected' : ''; ?>>List</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mtop15">
                            <div class="col-md-12">
                                <h5 class="bold"><?php echo _l('im_scoring_settings'); ?></h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="im_auto_sync_metrics">
                                        <input type="checkbox" name="im_auto_sync_metrics" id="im_auto_sync_metrics" value="1" <?php echo (isset($settings['im_auto_sync_metrics']) && $settings['im_auto_sync_metrics'] == '1') ? 'checked' : ''; ?>>
                                        <?php echo _l('im_auto_sync_metrics'); ?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="im_sync_frequency_hours"><?php echo _l('im_sync_frequency'); ?> (heures)</label>
                                    <input type="number" name="im_sync_frequency_hours" id="im_sync_frequency_hours" class="form-control" value="<?php echo isset($settings['im_sync_frequency_hours']) ? $settings['im_sync_frequency_hours'] : '24'; ?>" min="1" max="168">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="im_fake_followers_threshold"><?php echo _l('im_fake_followers_threshold'); ?> (%)</label>
                                    <input type="number" name="im_fake_followers_threshold" id="im_fake_followers_threshold" class="form-control" value="<?php echo isset($settings['im_fake_followers_threshold']) ? $settings['im_fake_followers_threshold'] : '30'; ?>" min="0" max="100">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="im_enable_dark_mode">
                                        <input type="checkbox" name="im_enable_dark_mode" id="im_enable_dark_mode" value="1" <?php echo (isset($settings['im_enable_dark_mode']) && $settings['im_enable_dark_mode'] == '1') ? 'checked' : ''; ?>>
                                        <?php echo _l('im_enable_dark_mode'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mtop25">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check"></i> <?php echo _l('im_save'); ?>
                                </button>
                            </div>
                        </div>

                        <?php echo form_close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

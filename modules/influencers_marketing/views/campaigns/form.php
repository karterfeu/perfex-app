<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $campaign ? _l('im_edit_campaign') : _l('im_new_campaign'); ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open(admin_url('influencers_marketing/campaign_form' . ($campaign ? '/' . $campaign['id'] : ''))); ?>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name"><?php echo _l('im_campaign_name'); ?> *</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($campaign) ? e($campaign['name']) : ''; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description"><?php echo _l('im_campaign_description'); ?></label>
                                    <textarea name="description" id="description" class="form-control" rows="4"><?php echo isset($campaign) ? e($campaign['description']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status"><?php echo _l('im_status'); ?></label>
                                    <select name="status" id="status" class="form-control selectpicker">
                                        <option value="draft" <?php echo (isset($campaign) && $campaign['status'] == 'draft') ? 'selected' : ''; ?>><?php echo _l('im_campaign_draft'); ?></option>
                                        <option value="prospect" <?php echo (isset($campaign) && $campaign['status'] == 'prospect') ? 'selected' : ''; ?>><?php echo _l('im_campaign_prospect'); ?></option>
                                        <option value="active" <?php echo (isset($campaign) && $campaign['status'] == 'active') ? 'selected' : ''; ?>><?php echo _l('im_campaign_active'); ?></option>
                                        <option value="completed" <?php echo (isset($campaign) && $campaign['status'] == 'completed') ? 'selected' : ''; ?>><?php echo _l('im_campaign_completed'); ?></option>
                                        <option value="cancelled" <?php echo (isset($campaign) && $campaign['status'] == 'cancelled') ? 'selected' : ''; ?>><?php echo _l('im_campaign_cancelled'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="budget"><?php echo _l('im_budget'); ?></label>
                                    <input type="number" name="budget" id="budget" class="form-control" step="0.01" value="<?php echo isset($campaign) ? $campaign['budget'] : '0'; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date"><?php echo _l('im_start_date'); ?></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo isset($campaign) ? $campaign['start_date'] : ''; ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date"><?php echo _l('im_end_date'); ?></label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo isset($campaign) ? $campaign['end_date'] : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="brief"><?php echo _l('im_brief'); ?></label>
                                    <textarea name="brief" id="brief" class="form-control" rows="4"><?php echo isset($campaign) ? e($campaign['brief']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="goals"><?php echo _l('im_goals'); ?></label>
                                    <textarea name="goals" id="goals" class="form-control" rows="3"><?php echo isset($campaign) ? e($campaign['goals']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row mtop25">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check"></i> <?php echo _l('im_save'); ?>
                                </button>
                                <a href="<?php echo admin_url('influencers_marketing/campaigns'); ?>" class="btn btn-default">
                                    <?php echo _l('im_cancel'); ?>
                                </a>
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

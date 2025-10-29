<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $influencer ? _l('im_edit_influencer') : _l('im_new_influencer'); ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open_multipart(admin_url('influencers_marketing/influencer_form' . ($influencer ? '/' . $influencer['id'] : ''))); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstname"><?php echo _l('im_firstname'); ?> *</label>
                                    <input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($influencer) ? e($influencer['firstname']) : ''; ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastname"><?php echo _l('im_lastname'); ?> *</label>
                                    <input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($influencer) ? e($influencer['lastname']) : ''; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email"><?php echo _l('im_email'); ?></label>
                                    <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($influencer) ? e($influencer['email']) : ''; ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone"><?php echo _l('im_phone'); ?></label>
                                    <input type="text" name="phone" id="phone" class="form-control" value="<?php echo isset($influencer) ? e($influencer['phone']) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category"><?php echo _l('im_category'); ?></label>
                                    <input type="text" name="category" id="category" class="form-control" value="<?php echo isset($influencer) ? e($influencer['category']) : ''; ?>" placeholder="Ex: Beauté, Tech, Lifestyle...">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status"><?php echo _l('im_status'); ?></label>
                                    <select name="status" id="status" class="form-control selectpicker">
                                        <option value="prospect" <?php echo (isset($influencer) && $influencer['status'] == 'prospect') ? 'selected' : ''; ?>><?php echo _l('im_status_prospect'); ?></option>
                                        <option value="contacted" <?php echo (isset($influencer) && $influencer['status'] == 'contacted') ? 'selected' : ''; ?>><?php echo _l('im_status_contacted'); ?></option>
                                        <option value="negotiating" <?php echo (isset($influencer) && $influencer['status'] == 'negotiating') ? 'selected' : ''; ?>><?php echo _l('im_status_negotiating'); ?></option>
                                        <option value="active" <?php echo (isset($influencer) && $influencer['status'] == 'active') ? 'selected' : ''; ?>><?php echo _l('im_status_active'); ?></option>
                                        <option value="inactive" <?php echo (isset($influencer) && $influencer['status'] == 'inactive') ? 'selected' : ''; ?>><?php echo _l('im_status_inactive'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location"><?php echo _l('im_location'); ?></label>
                                    <input type="text" name="location" id="location" class="form-control" value="<?php echo isset($influencer) ? e($influencer['location']) : ''; ?>" placeholder="Ville">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="country"><?php echo _l('im_country'); ?></label>
                                    <input type="text" name="country" id="country" class="form-control" value="<?php echo isset($influencer) ? e($influencer['country']) : ''; ?>" placeholder="Pays">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="bio"><?php echo _l('im_bio'); ?></label>
                                    <textarea name="bio" id="bio" class="form-control" rows="4" placeholder="Biographie de l'influenceur..."><?php echo isset($influencer) ? e($influencer['bio']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes"><?php echo _l('im_notes'); ?></label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Notes internes..."><?php echo isset($influencer) ? e($influencer['notes']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rating"><?php echo _l('im_rating'); ?></label>
                                    <select name="rating" id="rating" class="form-control selectpicker">
                                        <option value="0" <?php echo (isset($influencer) && $influencer['rating'] == 0) ? 'selected' : ''; ?>>-</option>
                                        <option value="1" <?php echo (isset($influencer) && $influencer['rating'] == 1) ? 'selected' : ''; ?>>⭐</option>
                                        <option value="2" <?php echo (isset($influencer) && $influencer['rating'] == 2) ? 'selected' : ''; ?>>⭐⭐</option>
                                        <option value="3" <?php echo (isset($influencer) && $influencer['rating'] == 3) ? 'selected' : ''; ?>>⭐⭐⭐</option>
                                        <option value="4" <?php echo (isset($influencer) && $influencer['rating'] == 4) ? 'selected' : ''; ?>>⭐⭐⭐⭐</option>
                                        <option value="5" <?php echo (isset($influencer) && $influencer['rating'] == 5) ? 'selected' : ''; ?>>⭐⭐⭐⭐⭐</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="is_verified" value="1" <?php echo (isset($influencer) && $influencer['is_verified']) ? 'checked' : ''; ?>>
                                        <?php echo _l('im_is_verified'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="profile_picture"><?php echo _l('im_profile_picture'); ?></label>
                                    <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*">
                                    <?php if (isset($influencer) && $influencer['profile_picture']): ?>
                                        <p class="text-muted mtop10">
                                            <small>Image actuelle: <?php echo basename($influencer['profile_picture']); ?></small>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row mtop25">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check"></i> <?php echo _l('im_save'); ?>
                                </button>
                                <a href="<?php echo admin_url('influencers_marketing/influencers'); ?>" class="btn btn-default">
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

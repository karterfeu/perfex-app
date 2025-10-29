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

                        <!-- Social Media Section -->
                        <hr class="mtop25" />
                        <div class="row mtop25">
                            <div class="col-md-12">
                                <h4 class="bold">
                                    <i class="fa fa-share-alt"></i> <?php echo _l('im_social_accounts'); ?>
                                    <small class="text-muted">(<?php echo _l('optional'); ?>)</small>
                                </h4>
                                <p class="text-muted"><?php echo _l('add_social_media_accounts'); ?></p>
                            </div>
                        </div>

                        <?php
                        // Prepare existing social accounts data
                        $existing_accounts = [];
                        if (isset($influencer) && !empty($influencer['social_accounts'])) {
                            foreach ($influencer['social_accounts'] as $account) {
                                $existing_accounts[$account['platform']] = $account;
                            }
                        }
                        ?>

                        <!-- Instagram -->
                        <div class="panel panel-default mtop15">
                            <div class="panel-heading" style="background-color: #E1306C; color: white;">
                                <i class="fa fa-instagram"></i> <strong>Instagram</strong>
                            </div>
                            <div class="panel-body">
                                <?php if (isset($existing_accounts['instagram']['id'])): ?>
                                    <input type="hidden" name="social_accounts[instagram][id]" value="<?php echo $existing_accounts['instagram']['id']; ?>">
                                <?php endif; ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="instagram_username"><?php echo _l('im_username'); ?></label>
                                            <div class="input-group">
                                                <span class="input-group-addon">@</span>
                                                <input type="text" name="social_accounts[instagram][username]" id="instagram_username" class="form-control" placeholder="username" value="<?php echo isset($existing_accounts['instagram']) ? e($existing_accounts['instagram']['username']) : ''; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="instagram_url"><?php echo _l('im_profile_url'); ?></label>
                                            <input type="url" name="social_accounts[instagram][profile_url]" id="instagram_url" class="form-control" placeholder="https://instagram.com/username" value="<?php echo isset($existing_accounts['instagram']) ? e($existing_accounts['instagram']['profile_url']) : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="instagram_followers"><?php echo _l('im_followers_count'); ?></label>
                                            <input type="number" name="social_accounts[instagram][followers_count]" id="instagram_followers" class="form-control" placeholder="0" value="<?php echo isset($existing_accounts['instagram']) ? $existing_accounts['instagram']['followers_count'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="instagram_engagement"><?php echo _l('im_engagement_rate'); ?> (%)</label>
                                            <input type="number" name="social_accounts[instagram][engagement_rate]" id="instagram_engagement" class="form-control" step="0.01" placeholder="0.00" value="<?php echo isset($existing_accounts['instagram']) ? $existing_accounts['instagram']['engagement_rate'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <label>
                                                <input type="checkbox" name="social_accounts[instagram][is_verified]" value="1" <?php echo (isset($existing_accounts['instagram']) && $existing_accounts['instagram']['is_verified']) ? 'checked' : ''; ?>>
                                                <?php echo _l('im_is_verified'); ?>
                                            </label>
                                            <label class="mleft10">
                                                <input type="checkbox" name="social_accounts[instagram][is_primary]" value="1" <?php echo (isset($existing_accounts['instagram']) && $existing_accounts['instagram']['is_primary']) ? 'checked' : ''; ?>>
                                                <?php echo _l('im_is_primary'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TikTok -->
                        <div class="panel panel-default">
                            <div class="panel-heading" style="background-color: #000000; color: white;">
                                <i class="fa fa-music"></i> <strong>TikTok</strong>
                            </div>
                            <div class="panel-body">
                                <?php if (isset($existing_accounts['tiktok']['id'])): ?>
                                    <input type="hidden" name="social_accounts[tiktok][id]" value="<?php echo $existing_accounts['tiktok']['id']; ?>">
                                <?php endif; ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tiktok_username"><?php echo _l('im_username'); ?></label>
                                            <div class="input-group">
                                                <span class="input-group-addon">@</span>
                                                <input type="text" name="social_accounts[tiktok][username]" id="tiktok_username" class="form-control" placeholder="username" value="<?php echo isset($existing_accounts['tiktok']) ? e($existing_accounts['tiktok']['username']) : ''; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tiktok_url"><?php echo _l('im_profile_url'); ?></label>
                                            <input type="url" name="social_accounts[tiktok][profile_url]" id="tiktok_url" class="form-control" placeholder="https://tiktok.com/@username" value="<?php echo isset($existing_accounts['tiktok']) ? e($existing_accounts['tiktok']['profile_url']) : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tiktok_followers"><?php echo _l('im_followers_count'); ?></label>
                                            <input type="number" name="social_accounts[tiktok][followers_count]" id="tiktok_followers" class="form-control" placeholder="0" value="<?php echo isset($existing_accounts['tiktok']) ? $existing_accounts['tiktok']['followers_count'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tiktok_engagement"><?php echo _l('im_engagement_rate'); ?> (%)</label>
                                            <input type="number" name="social_accounts[tiktok][engagement_rate]" id="tiktok_engagement" class="form-control" step="0.01" placeholder="0.00" value="<?php echo isset($existing_accounts['tiktok']) ? $existing_accounts['tiktok']['engagement_rate'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <label>
                                                <input type="checkbox" name="social_accounts[tiktok][is_verified]" value="1" <?php echo (isset($existing_accounts['tiktok']) && $existing_accounts['tiktok']['is_verified']) ? 'checked' : ''; ?>>
                                                <?php echo _l('im_is_verified'); ?>
                                            </label>
                                            <label class="mleft10">
                                                <input type="checkbox" name="social_accounts[tiktok][is_primary]" value="1" <?php echo (isset($existing_accounts['tiktok']) && $existing_accounts['tiktok']['is_primary']) ? 'checked' : ''; ?>>
                                                <?php echo _l('im_is_primary'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- YouTube -->
                        <div class="panel panel-default">
                            <div class="panel-heading" style="background-color: #FF0000; color: white;">
                                <i class="fa fa-youtube-play"></i> <strong>YouTube</strong>
                            </div>
                            <div class="panel-body">
                                <?php if (isset($existing_accounts['youtube']['id'])): ?>
                                    <input type="hidden" name="social_accounts[youtube][id]" value="<?php echo $existing_accounts['youtube']['id']; ?>">
                                <?php endif; ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="youtube_username"><?php echo _l('im_username'); ?></label>
                                            <div class="input-group">
                                                <span class="input-group-addon">@</span>
                                                <input type="text" name="social_accounts[youtube][username]" id="youtube_username" class="form-control" placeholder="username" value="<?php echo isset($existing_accounts['youtube']) ? e($existing_accounts['youtube']['username']) : ''; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="youtube_url"><?php echo _l('im_profile_url'); ?></label>
                                            <input type="url" name="social_accounts[youtube][profile_url]" id="youtube_url" class="form-control" placeholder="https://youtube.com/@username" value="<?php echo isset($existing_accounts['youtube']) ? e($existing_accounts['youtube']['profile_url']) : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="youtube_followers"><?php echo _l('im_followers_count'); ?> (Abonnés)</label>
                                            <input type="number" name="social_accounts[youtube][followers_count]" id="youtube_followers" class="form-control" placeholder="0" value="<?php echo isset($existing_accounts['youtube']) ? $existing_accounts['youtube']['followers_count'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="youtube_engagement"><?php echo _l('im_engagement_rate'); ?> (%)</label>
                                            <input type="number" name="social_accounts[youtube][engagement_rate]" id="youtube_engagement" class="form-control" step="0.01" placeholder="0.00" value="<?php echo isset($existing_accounts['youtube']) ? $existing_accounts['youtube']['engagement_rate'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <label>
                                                <input type="checkbox" name="social_accounts[youtube][is_verified]" value="1" <?php echo (isset($existing_accounts['youtube']) && $existing_accounts['youtube']['is_verified']) ? 'checked' : ''; ?>>
                                                <?php echo _l('im_is_verified'); ?>
                                            </label>
                                            <label class="mleft10">
                                                <input type="checkbox" name="social_accounts[youtube][is_primary]" value="1" <?php echo (isset($existing_accounts['youtube']) && $existing_accounts['youtube']['is_primary']) ? 'checked' : ''; ?>>
                                                <?php echo _l('im_is_primary'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Facebook -->
                        <div class="panel panel-default">
                            <div class="panel-heading" style="background-color: #1877F2; color: white;">
                                <i class="fa fa-facebook"></i> <strong>Facebook</strong>
                            </div>
                            <div class="panel-body">
                                <?php if (isset($existing_accounts['facebook']['id'])): ?>
                                    <input type="hidden" name="social_accounts[facebook][id]" value="<?php echo $existing_accounts['facebook']['id']; ?>">
                                <?php endif; ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="facebook_username"><?php echo _l('im_username'); ?></label>
                                            <input type="text" name="social_accounts[facebook][username]" id="facebook_username" class="form-control" placeholder="username" value="<?php echo isset($existing_accounts['facebook']) ? e($existing_accounts['facebook']['username']) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="facebook_url"><?php echo _l('im_profile_url'); ?></label>
                                            <input type="url" name="social_accounts[facebook][profile_url]" id="facebook_url" class="form-control" placeholder="https://facebook.com/username" value="<?php echo isset($existing_accounts['facebook']) ? e($existing_accounts['facebook']['profile_url']) : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="facebook_followers"><?php echo _l('im_followers_count'); ?></label>
                                            <input type="number" name="social_accounts[facebook][followers_count]" id="facebook_followers" class="form-control" placeholder="0" value="<?php echo isset($existing_accounts['facebook']) ? $existing_accounts['facebook']['followers_count'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="facebook_engagement"><?php echo _l('im_engagement_rate'); ?> (%)</label>
                                            <input type="number" name="social_accounts[facebook][engagement_rate]" id="facebook_engagement" class="form-control" step="0.01" placeholder="0.00" value="<?php echo isset($existing_accounts['facebook']) ? $existing_accounts['facebook']['engagement_rate'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <label>
                                                <input type="checkbox" name="social_accounts[facebook][is_verified]" value="1" <?php echo (isset($existing_accounts['facebook']) && $existing_accounts['facebook']['is_verified']) ? 'checked' : ''; ?>>
                                                <?php echo _l('im_is_verified'); ?>
                                            </label>
                                            <label class="mleft10">
                                                <input type="checkbox" name="social_accounts[facebook][is_primary]" value="1" <?php echo (isset($existing_accounts['facebook']) && $existing_accounts['facebook']['is_primary']) ? 'checked' : ''; ?>>
                                                <?php echo _l('im_is_primary'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- X (Twitter) -->
                        <div class="panel panel-default">
                            <div class="panel-heading" style="background-color: #000000; color: white;">
                                <i class="fa fa-twitter"></i> <strong>X (Twitter)</strong>
                            </div>
                            <div class="panel-body">
                                <?php if (isset($existing_accounts['twitter']['id'])): ?>
                                    <input type="hidden" name="social_accounts[twitter][id]" value="<?php echo $existing_accounts['twitter']['id']; ?>">
                                <?php endif; ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="twitter_username"><?php echo _l('im_username'); ?></label>
                                            <div class="input-group">
                                                <span class="input-group-addon">@</span>
                                                <input type="text" name="social_accounts[twitter][username]" id="twitter_username" class="form-control" placeholder="username" value="<?php echo isset($existing_accounts['twitter']) ? e($existing_accounts['twitter']['username']) : ''; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="twitter_url"><?php echo _l('im_profile_url'); ?></label>
                                            <input type="url" name="social_accounts[twitter][profile_url]" id="twitter_url" class="form-control" placeholder="https://x.com/username" value="<?php echo isset($existing_accounts['twitter']) ? e($existing_accounts['twitter']['profile_url']) : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="twitter_followers"><?php echo _l('im_followers_count'); ?></label>
                                            <input type="number" name="social_accounts[twitter][followers_count]" id="twitter_followers" class="form-control" placeholder="0" value="<?php echo isset($existing_accounts['twitter']) ? $existing_accounts['twitter']['followers_count'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="twitter_engagement"><?php echo _l('im_engagement_rate'); ?> (%)</label>
                                            <input type="number" name="social_accounts[twitter][engagement_rate]" id="twitter_engagement" class="form-control" step="0.01" placeholder="0.00" value="<?php echo isset($existing_accounts['twitter']) ? $existing_accounts['twitter']['engagement_rate'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <label>
                                                <input type="checkbox" name="social_accounts[twitter][is_verified]" value="1" <?php echo (isset($existing_accounts['twitter']) && $existing_accounts['twitter']['is_verified']) ? 'checked' : ''; ?>>
                                                <?php echo _l('im_is_verified'); ?>
                                            </label>
                                            <label class="mleft10">
                                                <input type="checkbox" name="social_accounts[twitter][is_primary]" value="1" <?php echo (isset($existing_accounts['twitter']) && $existing_accounts['twitter']['is_primary']) ? 'checked' : ''; ?>>
                                                <?php echo _l('im_is_primary'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Snapchat -->
                        <div class="panel panel-default">
                            <div class="panel-heading" style="background-color: #FFFC00; color: #000;">
                                <i class="fa fa-snapchat-ghost"></i> <strong>Snapchat</strong>
                            </div>
                            <div class="panel-body">
                                <?php if (isset($existing_accounts['snapchat']['id'])): ?>
                                    <input type="hidden" name="social_accounts[snapchat][id]" value="<?php echo $existing_accounts['snapchat']['id']; ?>">
                                <?php endif; ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="snapchat_username"><?php echo _l('im_username'); ?></label>
                                            <input type="text" name="social_accounts[snapchat][username]" id="snapchat_username" class="form-control" placeholder="username" value="<?php echo isset($existing_accounts['snapchat']) ? e($existing_accounts['snapchat']['username']) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="snapchat_url"><?php echo _l('im_profile_url'); ?></label>
                                            <input type="url" name="social_accounts[snapchat][profile_url]" id="snapchat_url" class="form-control" placeholder="https://snapchat.com/add/username" value="<?php echo isset($existing_accounts['snapchat']) ? e($existing_accounts['snapchat']['profile_url']) : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="snapchat_followers"><?php echo _l('im_followers_count'); ?></label>
                                            <input type="number" name="social_accounts[snapchat][followers_count]" id="snapchat_followers" class="form-control" placeholder="0" value="<?php echo isset($existing_accounts['snapchat']) ? $existing_accounts['snapchat']['followers_count'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="snapchat_engagement"><?php echo _l('im_engagement_rate'); ?> (%)</label>
                                            <input type="number" name="social_accounts[snapchat][engagement_rate]" id="snapchat_engagement" class="form-control" step="0.01" placeholder="0.00" value="<?php echo isset($existing_accounts['snapchat']) ? $existing_accounts['snapchat']['engagement_rate'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <label>
                                                <input type="checkbox" name="social_accounts[snapchat][is_verified]" value="1" <?php echo (isset($existing_accounts['snapchat']) && $existing_accounts['snapchat']['is_verified']) ? 'checked' : ''; ?>>
                                                <?php echo _l('im_is_verified'); ?>
                                            </label>
                                            <label class="mleft10">
                                                <input type="checkbox" name="social_accounts[snapchat][is_primary]" value="1" <?php echo (isset($existing_accounts['snapchat']) && $existing_accounts['snapchat']['is_primary']) ? 'checked' : ''; ?>>
                                                <?php echo _l('im_is_primary'); ?>
                                            </label>
                                        </div>
                                    </div>
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

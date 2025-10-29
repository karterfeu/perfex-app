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

                        <?php echo form_open(admin_url('influencers_marketing/campaign_form' . ($campaign ? '/' . $campaign['id'] : '')), ['id' => 'campaign-form']); ?>

                        <!-- Navigation Tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tab-informations" aria-controls="tab-informations" role="tab" data-toggle="tab">
                                    <i class="fa fa-info-circle"></i> <?php echo _l('im_campaign_info'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tab-influencers" aria-controls="tab-influencers" role="tab" data-toggle="tab">
                                    <i class="fa fa-users"></i> <?php echo _l('im_influencers'); ?>
                                    <span class="badge" id="influencers-count">0</span>
                                </a>
                            </li>
                            <?php if ($campaign): ?>
                            <li role="presentation">
                                <a href="#tab-contents" aria-controls="tab-contents" role="tab" data-toggle="tab">
                                    <i class="fa fa-link"></i> <?php echo _l('im_contents'); ?>
                                    <span class="badge" id="contents-count">0</span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mtop15">

                            <!-- TAB 1: INFORMATIONS -->
                            <div role="tabpanel" class="tab-pane active" id="tab-informations">
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
                            </div>

                            <!-- TAB 2: INFLUENCERS -->
                            <div role="tabpanel" class="tab-pane" id="tab-influencers">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> <?php echo _l('im_campaign_select_influencers_help'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="influencers_search"><?php echo _l('im_search_influencers'); ?></label>
                                            <input type="text" id="influencers_search" class="form-control" placeholder="<?php echo _l('im_search_by_name'); ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Selected Influencers -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="bold"><?php echo _l('im_selected_influencers'); ?></h5>
                                        <div id="selected-influencers-list" class="mtop10">
                                            <!-- Dynamically populated -->
                                        </div>
                                    </div>
                                </div>

                                <hr />

                                <!-- Available Influencers -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="bold"><?php echo _l('im_available_influencers'); ?></h5>
                                        <div id="available-influencers-list" class="mtop10">
                                            <!-- Dynamically populated via AJAX -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: CONTENTS (Only for existing campaigns) -->
                            <?php if ($campaign): ?>
                            <div role="tabpanel" class="tab-pane" id="tab-contents">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> <?php echo _l('im_campaign_contents_help'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div id="campaign-contents-container">
                                    <!-- Dynamically populated -->
                                </div>
                            </div>
                            <?php endif; ?>

                        </div>

                        <!-- Form Actions -->
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

<!-- Modal: Add Content -->
<div class="modal fade" id="modal-add-content" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo _l('im_add_content'); ?></h4>
            </div>
            <div class="modal-body">
                <form id="form-add-content">
                    <input type="hidden" name="campaign_id" value="<?php echo $campaign ? $campaign['id'] : ''; ?>">
                    <input type="hidden" name="influencer_id" id="content_influencer_id">

                    <div class="form-group">
                        <label for="content_url"><?php echo _l('im_content_url'); ?> *</label>
                        <input type="url" class="form-control" name="content_url" id="content_url" placeholder="https://instagram.com/p/..." required>
                        <small class="text-muted"><?php echo _l('im_content_url_help'); ?></small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="content_platform"><?php echo _l('im_platform'); ?></label>
                                <select class="form-control selectpicker" name="platform" id="content_platform">
                                    <option value="">Auto-détecté</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="tiktok">TikTok</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="twitter">Twitter/X</option>
                                    <option value="snapchat">Snapchat</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="content_type"><?php echo _l('im_content_type'); ?></label>
                                <select class="form-control selectpicker" name="content_type" id="content_type">
                                    <option value="">Auto-détecté</option>
                                    <option value="post">Post</option>
                                    <option value="video">Vidéo</option>
                                    <option value="reel">Reel</option>
                                    <option value="story">Story</option>
                                    <option value="short">Short</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="posted_at"><?php echo _l('im_posted_at'); ?></label>
                        <input type="datetime-local" class="form-control" name="posted_at" id="posted_at">
                    </div>

                    <hr />
                    <h5><?php echo _l('im_metrics'); ?> (<?php echo _l('im_optional'); ?>)</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="views_count"><?php echo _l('im_views'); ?></label>
                                <input type="number" class="form-control" name="views_count" id="views_count" placeholder="0">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="likes_count"><?php echo _l('im_likes'); ?></label>
                                <input type="number" class="form-control" name="likes_count" id="likes_count" placeholder="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="comments_count"><?php echo _l('im_comments'); ?></label>
                                <input type="number" class="form-control" name="comments_count" id="comments_count" placeholder="0">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="shares_count"><?php echo _l('im_shares'); ?></label>
                                <input type="number" class="form-control" name="shares_count" id="shares_count" placeholder="0">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('im_cancel'); ?></button>
                <button type="button" class="btn btn-primary" id="btn-save-content">
                    <i class="fa fa-check"></i> <?php echo _l('im_add'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Campaign Form JavaScript
$(document).ready(function() {
    <?php if ($campaign): ?>
    const campaignId = <?php echo $campaign['id']; ?>;
    loadCampaignInfluencers();
    loadCampaignContents();
    <?php endif; ?>

    loadAvailableInfluencers();

    // Search influencers
    $('#influencers_search').on('keyup', function() {
        const query = $(this).val();
        loadAvailableInfluencers(query);
    });

    // Add content button handler
    $(document).on('click', '.btn-add-content', function() {
        const influencerId = $(this).data('influencer-id');
        $('#content_influencer_id').val(influencerId);
        $('#modal-add-content').modal('show');
    });

    // Save content
    $('#btn-save-content').on('click', function() {
        const formData = $('#form-add-content').serialize();

        $.post(admin_url + 'influencers_marketing/add_campaign_content', formData, function(response) {
            if (response.success) {
                alert_float('success', '<?php echo _l('im_content_added'); ?>');
                $('#modal-add-content').modal('hide');
                $('#form-add-content')[0].reset();
                loadCampaignContents();
            } else {
                alert_float('danger', response.message || '<?php echo _l('im_error_occurred'); ?>');
            }
        });
    });
});

function loadAvailableInfluencers(search = '') {
    $.get(admin_url + 'influencers_marketing/get_available_influencers', { search: search }, function(response) {
        let html = '<div class="row">';

        if (response.influencers && response.influencers.length > 0) {
            response.influencers.forEach(function(inf) {
                const isSelected = selectedInfluencers.includes(inf.id);

                html += `
                    <div class="col-md-6 mtop10">
                        <div class="panel panel-default" style="margin-bottom: 0;">
                            <div class="panel-body">
                                <div class="media">
                                    <div class="media-left">
                                        <img src="${inf.profile_picture || 'https://via.placeholder.com/50'}"
                                             alt="${inf.firstname}"
                                             style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                    </div>
                                    <div class="media-body">
                                        <h5 class="media-heading">${inf.firstname} ${inf.lastname}</h5>
                                        <p class="text-muted no-margin">
                                            <small>
                                                <i class="fa fa-star"></i> Score: ${inf.influence_score || 0} |
                                                ${inf.category || 'N/A'}
                                            </small>
                                        </p>
                                        <button type="button"
                                                class="btn btn-xs ${isSelected ? 'btn-danger' : 'btn-success'} mtop5 btn-toggle-influencer"
                                                data-id="${inf.id}"
                                                data-name="${inf.firstname} ${inf.lastname}">
                                            <i class="fa fa-${isSelected ? 'minus' : 'plus'}"></i>
                                            ${isSelected ? '<?php echo _l('im_remove'); ?>' : '<?php echo _l('im_add'); ?>'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html += '<div class="col-md-12"><p class="text-muted"><?php echo _l('im_no_influencers_found'); ?></p></div>';
        }

        html += '</div>';
        $('#available-influencers-list').html(html);
    });
}

let selectedInfluencers = [];

$(document).on('click', '.btn-toggle-influencer', function() {
    const id = $(this).data('id');
    const name = $(this).data('name');

    const index = selectedInfluencers.indexOf(id);
    if (index > -1) {
        // Remove
        selectedInfluencers.splice(index, 1);
    } else {
        // Add
        selectedInfluencers.push(id);
    }

    updateSelectedInfluencersList();
    updateInfluencersCount();
    loadAvailableInfluencers($('#influencers_search').val());
});

function updateSelectedInfluencersList() {
    if (selectedInfluencers.length === 0) {
        $('#selected-influencers-list').html('<p class="text-muted"><?php echo _l('im_no_influencers_selected'); ?></p>');
        return;
    }

    let html = '<input type="hidden" name="influencer_ids" value="' + selectedInfluencers.join(',') + '">';
    html += '<div class="alert alert-success">';
    html += '<strong>' + selectedInfluencers.length + '</strong> influenceur(s) sélectionné(s)';
    html += '</div>';

    $('#selected-influencers-list').html(html);
}

function updateInfluencersCount() {
    $('#influencers-count').text(selectedInfluencers.length);
}

<?php if ($campaign): ?>
function loadCampaignInfluencers() {
    $.get(admin_url + 'influencers_marketing/get_campaign_influencers/<?php echo $campaign['id']; ?>', function(response) {
        if (response.influencers) {
            selectedInfluencers = response.influencers.map(inf => inf.influencer_id);
            updateSelectedInfluencersList();
            updateInfluencersCount();
        }
    });
}

function loadCampaignContents() {
    $.get(admin_url + 'influencers_marketing/get_campaign_contents_grouped/<?php echo $campaign['id']; ?>', function(response) {
        let html = '';
        let totalContents = 0;

        if (response.influencers && response.influencers.length > 0) {
            response.influencers.forEach(function(inf) {
                html += `
                    <div class="panel panel-default">
                        <div class="panel-heading" style="background-color: #f5f5f5;">
                            <strong>
                                <i class="fa fa-user"></i> ${inf.firstname} ${inf.lastname}
                                ${inf.username ? ' (@' + inf.username + ')' : ''}
                                ${inf.platform ? ' - ' + inf.platform.charAt(0).toUpperCase() + inf.platform.slice(1) : ''}
                            </strong>
                            <button type="button" class="btn btn-xs btn-success pull-right btn-add-content" data-influencer-id="${inf.influencer_id}">
                                <i class="fa fa-plus"></i> <?php echo _l('im_add_content'); ?>
                            </button>
                        </div>
                        <div class="panel-body">
                `;

                if (inf.contents && inf.contents.length > 0) {
                    totalContents += inf.contents.length;
                    inf.contents.forEach(function(content) {
                        const platformIcon = getPlatformIcon(content.platform);
                        const typeLabel = content.content_type.charAt(0).toUpperCase() + content.content_type.slice(1);

                        html += `
                            <div class="alert alert-info" style="position: relative;">
                                <button type="button" class="close btn-delete-content" data-id="${content.id}" style="margin-top: -2px;">
                                    <span>&times;</span>
                                </button>
                                <h5>
                                    ${platformIcon} ${typeLabel} - ${content.platform.charAt(0).toUpperCase() + content.platform.slice(1)}
                                </h5>
                                <p>
                                    <i class="fa fa-link"></i>
                                    <a href="${content.content_url}" target="_blank">${content.content_url}</a>
                                </p>
                                <div class="row">
                                    <div class="col-md-3">
                                        <small><i class="fa fa-eye"></i> ${formatNumber(content.views_count)} vues</small>
                                    </div>
                                    <div class="col-md-3">
                                        <small><i class="fa fa-heart"></i> ${formatNumber(content.likes_count)} likes</small>
                                    </div>
                                    <div class="col-md-3">
                                        <small><i class="fa fa-comment"></i> ${formatNumber(content.comments_count)} commentaires</small>
                                    </div>
                                    <div class="col-md-3">
                                        <small><i class="fa fa-share"></i> ${formatNumber(content.shares_count)} partages</small>
                                    </div>
                                </div>
                                ${content.posted_at ? '<p class="mtop5"><small class="text-muted"><i class="fa fa-clock-o"></i> Publié le ' + formatDate(content.posted_at) + '</small></p>' : ''}
                            </div>
                        `;
                    });
                } else {
                    html += '<p class="text-muted"><?php echo _l('im_no_contents_yet'); ?></p>';
                }

                html += `
                        </div>
                    </div>
                `;
            });
        } else {
            html = '<div class="alert alert-warning"><?php echo _l('im_add_influencers_first'); ?></div>';
        }

        $('#campaign-contents-container').html(html);
        $('#contents-count').text(totalContents);
    });
}

// Delete content
$(document).on('click', '.btn-delete-content', function() {
    if (confirm('<?php echo _l('im_confirm_delete_content'); ?>')) {
        const contentId = $(this).data('id');

        $.post(admin_url + 'influencers_marketing/delete_campaign_content/' + contentId, function(response) {
            if (response.success) {
                alert_float('success', '<?php echo _l('im_content_deleted'); ?>');
                loadCampaignContents();
            } else {
                alert_float('danger', '<?php echo _l('im_error_occurred'); ?>');
            }
        });
    }
});

function getPlatformIcon(platform) {
    const icons = {
        instagram: '<i class="fa fa-instagram"></i>',
        tiktok: '<i class="fa fa-music"></i>',
        youtube: '<i class="fa fa-youtube-play"></i>',
        facebook: '<i class="fa fa-facebook"></i>',
        twitter: '<i class="fa fa-twitter"></i>',
        snapchat: '<i class="fa fa-snapchat-ghost"></i>'
    };
    return icons[platform] || '<i class="fa fa-link"></i>';
}

function formatNumber(num) {
    if (!num) return '0';
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('fr-FR');
}
<?php endif; ?>
</script>

<?php init_tail(); ?>

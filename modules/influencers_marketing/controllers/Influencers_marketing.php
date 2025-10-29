<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Influencers_marketing extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('influencers_marketing/influencers_model');
        $this->load->model('influencers_marketing/campaigns_model');
    }

    /**
     * Dashboard
     */
    public function index()
    {
        return $this->dashboard();
    }

    /**
     * Dashboard with statistics and overview
     */
    public function dashboard()
    {
        if (!has_permission('influencers_marketing', '', 'view')) {
            access_denied('influencers_marketing');
        }

        $data['title'] = _l('im_dashboard');
        $data['influencer_stats'] = $this->influencers_model->get_dashboard_stats();
        $data['campaign_stats'] = $this->campaigns_model->get_dashboard_stats();

        // Recent influencers
        $data['recent_influencers'] = $this->influencers_model->get_influencers([
            'limit' => 5,
            'offset' => 0,
            'order_by' => 'created_at',
            'order_direction' => 'DESC'
        ]);

        // Active campaigns
        $data['active_campaigns'] = $this->campaigns_model->get_campaigns([
            'status' => 'active',
            'limit' => 5,
            'offset' => 0
        ]);

        $this->load->view('dashboard', $data);
    }

    /**
     * Influencers list
     */
    public function influencers()
    {
        if (!has_permission('influencers_marketing', '', 'view')) {
            access_denied('influencers_marketing');
        }

        // Check if it's an AJAX request for data
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('influencers_marketing');
        }

        $data['title'] = _l('im_influencers');
        $data['tags'] = $this->influencers_model->get_all_tags();
        $data['staff_members'] = $this->staff_model->get();

        $this->load->view('influencers/list', $data);
    }

    /**
     * View influencer profile
     */
    public function influencer($id = '')
    {
        if (!has_permission('influencers_marketing', '', 'view')) {
            access_denied('influencers_marketing');
        }

        if ($id == '') {
            redirect(admin_url('influencers_marketing/influencers'));
        }

        $data['influencer'] = $this->influencers_model->get($id);

        if (!$data['influencer']) {
            show_404();
        }

        $data['title'] = $data['influencer']['firstname'] . ' ' . $data['influencer']['lastname'];
        $data['tags'] = $this->influencers_model->get_all_tags();
        $data['staff_members'] = $this->staff_model->get();

        // Get campaigns this influencer is part of
        $this->db->select('c.*');
        $this->db->from(db_prefix() . 'im_campaigns c');
        $this->db->join(db_prefix() . 'im_campaign_influencers ci', 'ci.campaign_id = c.id');
        $this->db->where('ci.influencer_id', $id);
        $data['influencer_campaigns'] = $this->db->get()->result_array();

        $this->load->view('influencers/profile', $data);
    }

    /**
     * Add/Edit influencer
     */
    public function influencer_form($id = '')
    {
        if ($id == '') {
            if (!has_permission('influencers_marketing', '', 'create')) {
                access_denied('influencers_marketing');
            }
        } else {
            if (!has_permission('influencers_marketing', '', 'edit')) {
                access_denied('influencers_marketing');
            }
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            try {
                // Transform social_accounts from nested array to array of arrays with platform key
                if (isset($data['social_accounts']) && is_array($data['social_accounts'])) {
                    $transformed_social_accounts = [];
                    foreach ($data['social_accounts'] as $platform => $account_data) {
                        // Only add if at least username or profile_url is provided
                        if (!empty($account_data['username']) || !empty($account_data['profile_url'])) {
                            $account_data['platform'] = $platform;

                            // Convert is_verified and is_primary checkboxes to boolean
                            $account_data['is_verified'] = isset($account_data['is_verified']) ? 1 : 0;
                            $account_data['is_primary'] = isset($account_data['is_primary']) ? 1 : 0;

                            // Convert empty numeric fields to 0 or null
                            $account_data['followers_count'] = (!empty($account_data['followers_count'])) ? (int)$account_data['followers_count'] : 0;
                            $account_data['engagement_rate'] = (!empty($account_data['engagement_rate'])) ? (float)$account_data['engagement_rate'] : 0;

                            $transformed_social_accounts[] = $account_data;
                        }
                    }
                    $data['social_accounts'] = $transformed_social_accounts;
                }

                // Handle file upload
                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['name'] != '') {
                    $upload_path = FCPATH . 'uploads/influencers_marketing/profiles/';

                    // Create directory if it doesn't exist
                    if (!file_exists($upload_path)) {
                        @mkdir($upload_path, 0755, true);
                    }

                    $config['upload_path'] = $upload_path;
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['max_size'] = 2048;
                    $config['encrypt_name'] = true;

                    $this->load->library('upload', $config);

                    if ($this->upload->do_upload('profile_picture')) {
                        $upload_data = $this->upload->data();
                        $data['profile_picture'] = 'uploads/influencers_marketing/profiles/' . $upload_data['file_name'];
                    }
                }

                if ($id == '') {
                    $influencer_id = $this->influencers_model->add($data);
                    if ($influencer_id) {
                        set_alert('success', _l('added_successfully', _l('im_influencer')));
                        redirect(admin_url('influencers_marketing/influencer/' . $influencer_id));
                    } else {
                        set_alert('danger', 'Erreur lors de l\'ajout de l\'influenceur');
                        redirect(admin_url('influencers_marketing/influencer_form'));
                    }
                } else {
                    $success = $this->influencers_model->update($id, $data);
                    if ($success) {
                        set_alert('success', _l('updated_successfully', _l('im_influencer')));
                    } else {
                        set_alert('danger', 'Erreur lors de la mise à jour');
                    }
                    redirect(admin_url('influencers_marketing/influencer/' . $id));
                }
            } catch (Exception $e) {
                log_message('error', 'Influencer form error: ' . $e->getMessage());
                set_alert('danger', 'Une erreur est survenue : ' . $e->getMessage());
                redirect(admin_url('influencers_marketing/influencer_form' . ($id ? '/' . $id : '')));
            }
        }

        $data['influencer'] = null;
        if ($id != '') {
            $data['influencer'] = $this->influencers_model->get($id);
            if (!$data['influencer']) {
                show_404();
            }
        }

        $data['title'] = $id == '' ? _l('im_new_influencer') : _l('im_edit_influencer');
        $data['tags'] = $this->influencers_model->get_all_tags();
        $data['staff_members'] = $this->staff_model->get();

        $this->load->view('influencers/form', $data);
    }

    /**
     * Delete influencer
     */
    public function delete_influencer($id)
    {
        if (!has_permission('influencers_marketing', '', 'delete')) {
            access_denied('influencers_marketing');
        }

        if ($this->input->is_ajax_request()) {
            $success = $this->influencers_model->delete($id);
            echo json_encode(['success' => $success]);
        }
    }

    /**
     * Toggle favorite influencer
     */
    public function toggle_favorite($id)
    {
        if ($this->input->is_ajax_request()) {
            $result = $this->influencers_model->toggle_favorite($id);
            echo json_encode(['success' => $result !== false, 'is_favorite' => $result]);
        }
    }

    /**
     * Convert influencer to customer
     */
    public function convert_to_customer($id)
    {
        if (!has_permission('influencers_marketing', '', 'edit')) {
            access_denied('influencers_marketing');
        }

        $customer_id = $this->influencers_model->convert_to_customer($id);

        if ($customer_id) {
            set_alert('success', _l('im_converted_to_customer'));
        } else {
            set_alert('danger', _l('im_conversion_failed'));
        }

        redirect(admin_url('influencers_marketing/influencer/' . $id));
    }

    /**
     * Search influencers (AJAX autocomplete)
     */
    public function search()
    {
        if ($this->input->is_ajax_request()) {
            $query = $this->input->get('q');
            $results = $this->influencers_model->search($query);
            echo json_encode($results);
        }
    }

    /**
     * Add social account to influencer
     */
    public function add_social_account($influencer_id)
    {
        if (!has_permission('influencers_marketing', '', 'edit')) {
            access_denied('influencers_marketing');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['influencer_id'] = $influencer_id;

            $account_id = $this->influencers_model->add_social_account($data);

            if ($account_id) {
                // Recalculate influence score
                $this->influencers_model->calculate_and_update_score($influencer_id);

                set_alert('success', _l('im_social_account_added'));
            }

            redirect(admin_url('influencers_marketing/influencer/' . $influencer_id));
        }
    }

    /**
     * Update social account
     */
    public function update_social_account($account_id)
    {
        if (!has_permission('influencers_marketing', '', 'edit')) {
            access_denied('influencers_marketing');
        }

        if ($this->input->post() && $this->input->is_ajax_request()) {
            $data = $this->input->post();
            $success = $this->influencers_model->update_social_account($account_id, $data);

            // Get influencer_id to recalculate score
            $account = $this->db->get_where(db_prefix() . 'im_social_accounts', ['id' => $account_id])->row_array();
            if ($account) {
                $this->influencers_model->calculate_and_update_score($account['influencer_id']);
            }

            echo json_encode(['success' => $success]);
        }
    }

    /**
     * Delete social account
     */
    public function delete_social_account($account_id)
    {
        if (!has_permission('influencers_marketing', '', 'delete')) {
            access_denied('influencers_marketing');
        }

        if ($this->input->is_ajax_request()) {
            // Get influencer_id before deleting
            $account = $this->db->get_where(db_prefix() . 'im_social_accounts', ['id' => $account_id])->row_array();

            $success = $this->influencers_model->delete_social_account($account_id);

            // Recalculate score
            if ($success && $account) {
                $this->influencers_model->calculate_and_update_score($account['influencer_id']);
            }

            echo json_encode(['success' => $success]);
        }
    }

    /**
     * Add interaction
     */
    public function add_interaction($influencer_id)
    {
        if (!has_permission('influencers_marketing', '', 'create')) {
            access_denied('influencers_marketing');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['influencer_id'] = $influencer_id;

            $interaction_id = $this->influencers_model->add_interaction($data);

            if ($interaction_id) {
                set_alert('success', _l('im_interaction_added'));
            }

            redirect(admin_url('influencers_marketing/influencer/' . $influencer_id));
        }
    }

    /**
     * Campaigns list
     */
    public function campaigns()
    {
        if (!has_permission('influencers_marketing', '', 'view')) {
            access_denied('influencers_marketing');
        }

        $data['title'] = _l('im_campaigns');
        $data['campaigns'] = $this->campaigns_model->get_campaigns();

        $this->load->view('campaigns/list', $data);
    }

    /**
     * View campaign
     */
    public function campaign($id = '')
    {
        if (!has_permission('influencers_marketing', '', 'view')) {
            access_denied('influencers_marketing');
        }

        if ($id == '') {
            redirect(admin_url('influencers_marketing/campaigns'));
        }

        $data['campaign'] = $this->campaigns_model->get($id);

        if (!$data['campaign']) {
            show_404();
        }

        $data['title'] = $data['campaign']['name'];

        $this->load->view('campaigns/view', $data);
    }

    /**
     * Add/Edit campaign
     */
    public function campaign_form($id = '')
    {
        if ($id == '') {
            if (!has_permission('influencers_marketing', '', 'create')) {
                access_denied('influencers_marketing');
            }
        } else {
            if (!has_permission('influencers_marketing', '', 'edit')) {
                access_denied('influencers_marketing');
            }
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            // Extract influencer IDs if provided
            $influencer_ids = null;
            if (isset($data['influencer_ids']) && !empty($data['influencer_ids'])) {
                $influencer_ids = explode(',', $data['influencer_ids']);
                unset($data['influencer_ids']);
            }

            if ($id == '') {
                // Create new campaign
                $campaign_id = $this->campaigns_model->add($data);
                if ($campaign_id) {
                    // Add influencers to campaign if provided
                    if ($influencer_ids && is_array($influencer_ids)) {
                        foreach ($influencer_ids as $influencer_id) {
                            $this->campaigns_model->add_influencer_to_campaign($campaign_id, [
                                'influencer_id' => $influencer_id,
                                'status' => 'prospect',
                                'added_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }

                    set_alert('success', _l('added_successfully', _l('im_campaign')));
                    redirect(admin_url('influencers_marketing/campaign_form/' . $campaign_id));
                }
            } else {
                // Update existing campaign
                $success = $this->campaigns_model->update($id, $data);

                // Update influencers if provided
                if ($influencer_ids && is_array($influencer_ids)) {
                    // Remove all existing influencers
                    $this->db->where('campaign_id', $id);
                    $this->db->delete(db_prefix() . 'im_campaign_influencers');

                    // Add new ones
                    foreach ($influencer_ids as $influencer_id) {
                        $this->campaigns_model->add_influencer_to_campaign($id, [
                            'influencer_id' => $influencer_id,
                            'status' => 'prospect',
                            'added_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }

                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('im_campaign')));
                }
                redirect(admin_url('influencers_marketing/campaign_form/' . $id));
            }
        }

        $data['campaign'] = null;
        if ($id != '') {
            $data['campaign'] = $this->campaigns_model->get($id);
            if (!$data['campaign']) {
                show_404();
            }
        }

        $data['title'] = $id == '' ? _l('im_new_campaign') : _l('im_edit_campaign');
        $data['customers'] = $this->clients_model->get();

        $this->load->view('campaigns/form', $data);
    }

    /**
     * Delete campaign
     */
    public function delete_campaign($id)
    {
        if (!has_permission('influencers_marketing', '', 'delete')) {
            access_denied('influencers_marketing');
        }

        if ($this->input->is_ajax_request()) {
            $success = $this->campaigns_model->delete($id);
            echo json_encode(['success' => $success]);
        }
    }

    /**
     * Add influencer to campaign
     */
    public function add_influencer_to_campaign($campaign_id)
    {
        if (!has_permission('influencers_marketing', '', 'edit')) {
            access_denied('influencers_marketing');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['campaign_id'] = $campaign_id;

            $success = $this->campaigns_model->add_influencer_to_campaign($campaign_id, $data);

            if ($success) {
                set_alert('success', _l('im_influencer_added_to_campaign'));
            }

            redirect(admin_url('influencers_marketing/campaign/' . $campaign_id));
        }
    }

    /**
     * Remove influencer from campaign
     */
    public function remove_influencer_from_campaign($campaign_id, $influencer_id)
    {
        if (!has_permission('influencers_marketing', '', 'delete')) {
            access_denied('influencers_marketing');
        }

        if ($this->input->is_ajax_request()) {
            $success = $this->campaigns_model->remove_influencer_from_campaign($campaign_id, $influencer_id);
            echo json_encode(['success' => $success]);
        }
    }

    /**
     * Create invoice from campaign
     */
    public function create_invoice($campaign_id, $influencer_id)
    {
        if (!has_permission('influencers_marketing', '', 'create')) {
            access_denied('influencers_marketing');
        }

        $invoice_id = $this->campaigns_model->create_invoice($campaign_id, $influencer_id);

        if ($invoice_id) {
            set_alert('success', _l('im_invoice_created'));
            redirect(admin_url('invoices/list_invoices/' . $invoice_id));
        } else {
            set_alert('danger', _l('im_invoice_creation_failed'));
            redirect(admin_url('influencers_marketing/campaign/' . $campaign_id));
        }
    }

    /**
     * Create quote for influencer
     */
    public function create_quote($campaign_id, $influencer_id)
    {
        if (!has_permission('influencers_marketing', '', 'create')) {
            access_denied('influencers_marketing');
        }

        $quote_id = $this->campaigns_model->create_quote($campaign_id, $influencer_id);

        if ($quote_id) {
            set_alert('success', _l('im_quote_created'));
            redirect(admin_url('estimates/list_estimates/' . $quote_id));
        } else {
            set_alert('danger', _l('im_quote_creation_failed'));
            redirect(admin_url('influencers_marketing/campaign/' . $campaign_id));
        }
    }

    /**
     * Analytics page
     */
    public function analytics()
    {
        if (!has_permission('influencers_marketing', '', 'view')) {
            access_denied('influencers_marketing');
        }

        $data['title'] = _l('im_analytics');

        // Get influencer for analytics if specified
        $influencer_id = $this->input->get('influencer_id');
        if ($influencer_id) {
            $data['influencer'] = $this->influencers_model->get($influencer_id);
        }

        // Get campaign for analytics if specified
        $campaign_id = $this->input->get('campaign_id');
        if ($campaign_id) {
            $data['campaign'] = $this->campaigns_model->get($campaign_id);
        }

        $data['influencers'] = $this->influencers_model->get_influencers(['limit' => 1000, 'offset' => 0]);
        $data['campaigns'] = $this->campaigns_model->get_campaigns(['limit' => 1000, 'offset' => 0]);

        $this->load->view('analytics/index', $data);
    }

    /**
     * Get analytics data (AJAX)
     */
    public function get_analytics_data()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $type = $this->input->get('type');
        $id = $this->input->get('id');
        $days = $this->input->get('days') ?? 30;

        $data = [];

        switch ($type) {
            case 'influencer_metrics':
                // Get metrics history for all social accounts
                $social_accounts = $this->influencers_model->get_social_accounts($id);
                foreach ($social_accounts as $account) {
                    $metrics = $this->influencers_model->get_metrics_history($account['id'], $days);
                    $data[$account['platform']] = $metrics;
                }
                break;

            case 'campaign_performance':
                $campaign = $this->campaigns_model->get($id);
                $data = [
                    'roi' => $campaign['roi'],
                    'deliverables' => $campaign['deliverables'],
                    'influencers' => $campaign['influencers'],
                ];
                break;

            case 'top_performers':
                $data = $this->influencers_model->get_influencers([
                    'order_by' => 'influence_score',
                    'order_direction' => 'DESC',
                    'limit' => 10,
                    'offset' => 0
                ]);
                break;
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Settings page
     */
    public function settings()
    {
        if (!is_admin()) {
            access_denied('influencers_marketing');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            foreach ($data as $key => $value) {
                $this->db->where('name', $key);
                $exists = $this->db->get(db_prefix() . 'im_settings')->row();

                if ($exists) {
                    $this->db->where('name', $key);
                    $this->db->update(db_prefix() . 'im_settings', ['value' => $value]);
                } else {
                    $this->db->insert(db_prefix() . 'im_settings', [
                        'name' => $key,
                        'value' => $value,
                        'autoload' => 1
                    ]);
                }
            }

            set_alert('success', _l('settings_updated'));
            redirect(admin_url('influencers_marketing/settings'));
        }

        $data['title'] = _l('settings');

        // Get all settings
        $settings = $this->db->get(db_prefix() . 'im_settings')->result_array();
        $data['settings'] = [];
        foreach ($settings as $setting) {
            $data['settings'][$setting['name']] = $setting['value'];
        }

        $this->load->view('settings', $data);
    }

    /**
     * Export influencers to CSV
     */
    public function export_influencers()
    {
        if (!has_permission('influencers_marketing', '', 'view')) {
            access_denied('influencers_marketing');
        }

        $influencers = $this->influencers_model->get_influencers();

        $this->load->helper('download');
        $this->load->library('App_csv');

        $csv = new App_csv();
        $headers = [
            'ID',
            'Prénom',
            'Nom',
            'Email',
            'Téléphone',
            'Catégorie',
            'Score d\'influence',
            'Statut',
            'Note',
            'Localisation',
            'Pays',
            'Date de création'
        ];

        $csv->add_row($headers);

        foreach ($influencers as $influencer) {
            $csv->add_row([
                $influencer['id'],
                $influencer['firstname'],
                $influencer['lastname'],
                $influencer['email'],
                $influencer['phone'],
                $influencer['category'],
                $influencer['influence_score'],
                $influencer['status'],
                $influencer['rating'],
                $influencer['location'],
                $influencer['country'],
                $influencer['created_at']
            ]);
        }

        $csv->download('influenceurs_' . date('Y-m-d') . '.csv');
    }

    /**
     * Import influencers from CSV
     */
    public function import_influencers()
    {
        if (!has_permission('influencers_marketing', '', 'create')) {
            access_denied('influencers_marketing');
        }

        if ($this->input->post()) {
            $this->load->library('import/import_influencers', [], 'import');

            $config['upload_path'] = TEMP_FOLDER;
            $config['allowed_types'] = 'csv';
            $config['max_size'] = 10240;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file')) {
                $upload_data = $this->upload->data();
                $file_path = $upload_data['full_path'];

                $imported = $this->import->import_data($file_path);

                @unlink($file_path);

                if ($imported) {
                    set_alert('success', _l('im_import_successful', $imported));
                } else {
                    set_alert('danger', _l('im_import_failed'));
                }
            } else {
                set_alert('danger', $this->upload->display_errors('', ''));
            }

            redirect(admin_url('influencers_marketing/influencers'));
        }

        $data['title'] = _l('im_import_influencers');
        $this->load->view('influencers/import', $data);
    }

    /**
     * Bulk actions
     */
    public function bulk_action()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $action = $this->input->post('action');
        $ids = $this->input->post('ids');

        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $success = 0;

        switch ($action) {
            case 'delete':
                if (has_permission('influencers_marketing', '', 'delete')) {
                    foreach ($ids as $id) {
                        if ($this->influencers_model->delete($id)) {
                            $success++;
                        }
                    }
                }
                break;

            case 'mark_favorite':
                foreach ($ids as $id) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'im_influencers', ['is_favorite' => 1]);
                    $success++;
                }
                break;

            case 'update_status':
                $status = $this->input->post('status');
                foreach ($ids as $id) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'im_influencers', ['status' => $status]);
                    $success++;
                }
                break;

            case 'assign_staff':
                $staff_id = $this->input->post('staff_id');
                foreach ($ids as $id) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'im_influencers', ['staff_id' => $staff_id]);
                    $success++;
                }
                break;
        }

        echo json_encode(['success' => $success > 0, 'count' => $success]);
    }

    /**
     * Enable permissions for all staff roles (one-time setup)
     * Access via: admin/influencers_marketing/enable_staff_permissions
     */
    public function enable_staff_permissions()
    {
        if (!is_admin()) {
            access_denied('influencers_marketing');
        }

        // Get all roles
        $this->db->select('roleid, name');
        $roles = $this->db->get(db_prefix() . 'roles')->result_array();

        $updated = 0;
        $already_exists = 0;

        foreach ($roles as $role) {
            // Check if permission already exists for this role
            $this->db->where('permissionid', 'influencers_marketing');
            $this->db->where('roleid', $role['roleid']);
            $exists = $this->db->get(db_prefix() . 'staff_permissions')->row();

            if (!$exists) {
                // Create permission with view enabled by default
                $this->db->insert(db_prefix() . 'staff_permissions', [
                    'permissionid' => 'influencers_marketing',
                    'roleid' => $role['roleid'],
                    'view' => 1,
                    'view_own' => 1,
                    'create' => 0,
                    'edit' => 0,
                    'delete' => 0,
                ]);

                $updated++;
            } else {
                // Permission exists, enable view if disabled
                if ($exists->view == 0) {
                    $this->db->where('permissionid', 'influencers_marketing');
                    $this->db->where('roleid', $role['roleid']);
                    $this->db->update(db_prefix() . 'staff_permissions', [
                        'view' => 1,
                        'view_own' => 1,
                    ]);

                    $updated++;
                } else {
                    $already_exists++;
                }
            }
        }

        set_alert('success', "Permissions activées pour $updated rôle(s). $already_exists étaient déjà actives.");
        redirect(admin_url('influencers_marketing/settings'));
    }

    // ============================================
    // CAMPAIGN CONTENTS & INFLUENCERS AJAX METHODS
    // ============================================

    /**
     * Get available influencers for campaign selection (AJAX)
     */
    public function get_available_influencers()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $search = $this->input->get('search');

        $influencers = $this->influencers_model->get_influencers([
            'search' => $search,
            'limit' => 50,
            'offset' => 0
        ]);

        header('Content-Type: application/json');
        echo json_encode(['influencers' => $influencers]);
    }

    /**
     * Get influencers assigned to a campaign (AJAX)
     */
    public function get_campaign_influencers($campaign_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->db->select('influencer_id');
        $this->db->where('campaign_id', $campaign_id);
        $influencers = $this->db->get(db_prefix() . 'im_campaign_influencers')->result_array();

        header('Content-Type: application/json');
        echo json_encode(['influencers' => $influencers]);
    }

    /**
     * Get campaign contents grouped by influencer (AJAX)
     */
    public function get_campaign_contents_grouped($campaign_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $influencers = $this->campaigns_model->get_contents_by_influencer($campaign_id);

        header('Content-Type: application/json');
        echo json_encode(['influencers' => $influencers]);
    }

    /**
     * Add content to campaign (AJAX)
     */
    public function add_campaign_content()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!has_permission('influencers_marketing', '', 'create')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $data = $this->input->post();

        $content_id = $this->campaigns_model->add_content($data);

        if ($content_id) {
            echo json_encode(['success' => true, 'content_id' => $content_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add content']);
        }
    }

    /**
     * Delete campaign content (AJAX)
     */
    public function delete_campaign_content($content_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!has_permission('influencers_marketing', '', 'delete')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $success = $this->campaigns_model->delete_content($content_id);

        echo json_encode(['success' => $success]);
    }

    /**
     * Update content metrics (AJAX)
     */
    public function update_content_metrics($content_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!has_permission('influencers_marketing', '', 'edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $data = $this->input->post();

        $success = $this->campaigns_model->update_content($content_id, $data);

        echo json_encode(['success' => $success]);
    }
}

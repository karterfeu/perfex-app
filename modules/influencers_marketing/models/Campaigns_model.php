<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Campaigns_model extends App_Model
{
    private $table_campaigns;
    private $table_campaign_influencers;
    private $table_deliverables;

    public function __construct()
    {
        parent::__construct();

        $this->table_campaigns = db_prefix() . 'im_campaigns';
        $this->table_campaign_influencers = db_prefix() . 'im_campaign_influencers';
        $this->table_deliverables = db_prefix() . 'im_deliverables';
    }

    /**
     * Get all campaigns
     */
    public function get_campaigns($data = [])
    {
        $this->db->select($this->table_campaigns . '.*,
            COUNT(DISTINCT ci.influencer_id) as influencers_count,
            COUNT(DISTINCT d.id) as deliverables_count,
            SUM(CASE WHEN d.status = "published" THEN 1 ELSE 0 END) as published_deliverables');

        $this->db->from($this->table_campaigns);
        $this->db->join($this->table_campaign_influencers . ' ci', 'ci.campaign_id = ' . $this->table_campaigns . '.id', 'left');
        $this->db->join($this->table_deliverables . ' d', 'd.campaign_id = ' . $this->table_campaigns . '.id', 'left');

        // Filters
        if (isset($data['search']) && !empty($data['search'])) {
            $this->db->like($this->table_campaigns . '.name', $data['search']);
        }

        if (isset($data['status']) && !empty($data['status'])) {
            $this->db->where($this->table_campaigns . '.status', $data['status']);
        }

        if (isset($data['customer_id']) && !empty($data['customer_id'])) {
            $this->db->where($this->table_campaigns . '.customer_id', $data['customer_id']);
        }

        $this->db->group_by($this->table_campaigns . '.id');
        $this->db->order_by($this->table_campaigns . '.created_at', 'DESC');

        if (isset($data['limit']) && isset($data['offset'])) {
            $this->db->limit($data['limit'], $data['offset']);
        }

        return $this->db->get()->result_array();
    }

    /**
     * Get single campaign
     */
    public function get($id)
    {
        $this->db->where('id', $id);
        $campaign = $this->db->get($this->table_campaigns)->row_array();

        if ($campaign) {
            // Get influencers
            $campaign['influencers'] = $this->get_campaign_influencers($id);

            // Get deliverables
            $campaign['deliverables'] = $this->get_campaign_deliverables($id);

            // Get project info if linked
            if ($campaign['project_id']) {
                $this->db->select('name, status, start_date, deadline');
                $this->db->where('id', $campaign['project_id']);
                $campaign['project_info'] = $this->db->get(db_prefix() . 'projects')->row_array();
            }

            // Get customer info if linked
            if ($campaign['customer_id']) {
                $this->db->select('company, phonenumber, website');
                $this->db->where('userid', $campaign['customer_id']);
                $campaign['customer_info'] = $this->db->get(db_prefix() . 'clients')->row_array();
            }

            // Calculate ROI
            $campaign['roi'] = $this->calculate_roi($id);
        }

        return $campaign;
    }

    /**
     * Add campaign
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = get_staff_user_id();

        // Handle influencers separately
        if (isset($data['influencers'])) {
            $influencers = $data['influencers'];
            unset($data['influencers']);
        }

        $this->db->insert($this->table_campaigns, $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            // Add influencers
            if (isset($influencers) && is_array($influencers)) {
                foreach ($influencers as $influencer) {
                    $this->add_influencer_to_campaign($insert_id, $influencer);
                }
            }

            // Create project if requested
            if (isset($data['create_project']) && $data['create_project']) {
                $this->create_project_from_campaign($insert_id);
            }

            $this->log_campaign_activity($insert_id, 'Campagne créée');
        }

        return $insert_id;
    }

    /**
     * Update campaign
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Handle influencers separately
        if (isset($data['influencers'])) {
            unset($data['influencers']);
        }

        $this->db->where('id', $id);
        $result = $this->db->update($this->table_campaigns, $data);

        if ($result) {
            $this->log_campaign_activity($id, 'Campagne mise à jour');
        }

        return $result;
    }

    /**
     * Delete campaign
     */
    public function delete($id)
    {
        // Delete campaign influencers
        $this->db->where('campaign_id', $id);
        $this->db->delete($this->table_campaign_influencers);

        // Delete deliverables
        $this->db->where('campaign_id', $id);
        $this->db->delete($this->table_deliverables);

        // Delete campaign
        $this->db->where('id', $id);
        return $this->db->delete($this->table_campaigns);
    }

    /**
     * Get campaign influencers
     */
    public function get_campaign_influencers($campaign_id)
    {
        $this->db->select('ci.*,
            i.firstname, i.lastname, i.email, i.profile_picture, i.influence_score,
            CONCAT(i.firstname, " ", i.lastname) as full_name');
        $this->db->from($this->table_campaign_influencers . ' ci');
        $this->db->join(db_prefix() . 'im_influencers i', 'i.id = ci.influencer_id');
        $this->db->where('ci.campaign_id', $campaign_id);
        $this->db->order_by('ci.added_at', 'ASC');

        return $this->db->get()->result_array();
    }

    /**
     * Add influencer to campaign
     */
    public function add_influencer_to_campaign($campaign_id, $influencer_data)
    {
        $data = [
            'campaign_id' => $campaign_id,
            'influencer_id' => $influencer_data['influencer_id'],
            'status' => $influencer_data['status'] ?? 'invited',
            'compensation_type' => $influencer_data['compensation_type'] ?? 'paid',
            'compensation_amount' => $influencer_data['compensation_amount'] ?? 0,
            'currency' => $influencer_data['currency'] ?? 'EUR',
            'added_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert($this->table_campaign_influencers, $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $this->log_campaign_activity($campaign_id, 'Influenceur ajouté à la campagne');

            // Create quote if requested
            if (isset($influencer_data['create_quote']) && $influencer_data['create_quote']) {
                $this->create_quote($campaign_id, $influencer_data['influencer_id']);
            }
        }

        return $insert_id;
    }

    /**
     * Remove influencer from campaign
     */
    public function remove_influencer_from_campaign($campaign_id, $influencer_id)
    {
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('influencer_id', $influencer_id);
        $result = $this->db->delete($this->table_campaign_influencers);

        if ($result) {
            $this->log_campaign_activity($campaign_id, 'Influenceur retiré de la campagne');
        }

        return $result;
    }

    /**
     * Update campaign influencer status
     */
    public function update_campaign_influencer_status($campaign_id, $influencer_id, $status)
    {
        $data = ['status' => $status];

        if ($status == 'accepted') {
            $data['accepted_at'] = date('Y-m-d H:i:s');
        } elseif ($status == 'completed') {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('influencer_id', $influencer_id);
        return $this->db->update($this->table_campaign_influencers, $data);
    }

    /**
     * Get campaign deliverables
     */
    public function get_campaign_deliverables($campaign_id)
    {
        $this->db->select('d.*,
            CONCAT(i.firstname, " ", i.lastname) as influencer_name,
            i.profile_picture');
        $this->db->from($this->table_deliverables . ' d');
        $this->db->join(db_prefix() . 'im_influencers i', 'i.id = d.influencer_id');
        $this->db->where('d.campaign_id', $campaign_id);
        $this->db->order_by('d.due_date', 'ASC');

        return $this->db->get()->result_array();
    }

    /**
     * Add deliverable
     */
    public function add_deliverable($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert($this->table_deliverables, $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            // Create task in Perfex if requested
            if (isset($data['create_task']) && $data['create_task']) {
                $this->create_task_from_deliverable($insert_id);
            }

            $this->log_campaign_activity($data['campaign_id'], 'Livrable ajouté : ' . $data['title']);
        }

        return $insert_id;
    }

    /**
     * Update deliverable
     */
    public function update_deliverable($id, $data)
    {
        $deliverable = $this->get_deliverable($id);

        if ($data['status'] == 'approved' && !$deliverable['approved_at']) {
            $data['approved_at'] = date('Y-m-d H:i:s');
        } elseif ($data['status'] == 'published' && !$deliverable['published_at']) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where('id', $id);
        $result = $this->db->update($this->table_deliverables, $data);

        if ($result && $deliverable) {
            $this->log_campaign_activity($deliverable['campaign_id'], 'Livrable mis à jour : ' . $deliverable['title']);

            // Update task if linked
            if ($deliverable['task_id'] && isset($data['status'])) {
                $this->update_task_status($deliverable['task_id'], $data['status']);
            }
        }

        return $result;
    }

    /**
     * Get single deliverable
     */
    public function get_deliverable($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table_deliverables)->row_array();
    }

    /**
     * Delete deliverable
     */
    public function delete_deliverable($id)
    {
        $deliverable = $this->get_deliverable($id);

        if ($deliverable) {
            $this->db->where('id', $id);
            $result = $this->db->delete($this->table_deliverables);

            if ($result) {
                $this->log_campaign_activity($deliverable['campaign_id'], 'Livrable supprimé : ' . $deliverable['title']);
            }

            return $result;
        }

        return false;
    }

    /**
     * Calculate campaign ROI
     */
    public function calculate_roi($campaign_id)
    {
        $campaign = $this->db->get_where($this->table_campaigns, ['id' => $campaign_id])->row_array();

        if (!$campaign) {
            return null;
        }

        $investment = $campaign['actual_cost'] > 0 ? $campaign['actual_cost'] : $campaign['budget'];
        $return = $campaign['revenue_generated'];

        if ($investment > 0) {
            $roi_percentage = (($return - $investment) / $investment) * 100;
            return [
                'investment' => $investment,
                'return' => $return,
                'profit' => $return - $investment,
                'roi_percentage' => round($roi_percentage, 2),
            ];
        }

        return [
            'investment' => $investment,
            'return' => $return,
            'profit' => 0,
            'roi_percentage' => 0,
        ];
    }

    /**
     * Create invoice from campaign
     */
    public function create_invoice($campaign_id, $influencer_id)
    {
        $this->load->model('invoices_model');
        $this->load->model('influencers_marketing/influencers_model');

        $campaign = $this->get($campaign_id);
        $influencer = $this->influencers_model->get($influencer_id);

        if (!$campaign || !$influencer) {
            return false;
        }

        // Get campaign influencer details
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('influencer_id', $influencer_id);
        $campaign_influencer = $this->db->get($this->table_campaign_influencers)->row_array();

        if (!$campaign_influencer) {
            return false;
        }

        // Make sure influencer is converted to customer
        if (!$influencer['customer_id']) {
            $customer_id = $this->influencers_model->convert_to_customer($influencer_id);
        } else {
            $customer_id = $influencer['customer_id'];
        }

        if (!$customer_id) {
            return false;
        }

        // Get deliverables for this influencer in this campaign
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('influencer_id', $influencer_id);
        $deliverables = $this->db->get($this->table_deliverables)->result_array();

        // Prepare invoice data
        $invoice_data = [
            'clientid' => $customer_id,
            'number' => get_option('next_invoice_number'),
            'date' => date('Y-m-d'),
            'duedate' => date('Y-m-d', strtotime('+30 days')),
            'currency' => $campaign_influencer['currency'],
            'subtotal' => $campaign_influencer['compensation_amount'],
            'total' => $campaign_influencer['compensation_amount'],
            'adminnote' => 'Facture générée automatiquement pour la campagne : ' . $campaign['name'],
            'terms' => '',
            'clientnote' => 'Merci pour votre collaboration sur la campagne ' . $campaign['name'],
        ];

        $invoice_id = $this->invoices_model->add($invoice_data);

        if ($invoice_id) {
            // Add invoice items
            $item_order = 1;

            // Main compensation
            $item_data = [
                'rel_id' => $invoice_id,
                'rel_type' => 'invoice',
                'description' => 'Collaboration campagne : ' . $campaign['name'],
                'long_description' => $campaign['description'] ?? '',
                'qty' => 1,
                'rate' => $campaign_influencer['compensation_amount'],
                'item_order' => $item_order++,
            ];
            $this->db->insert(db_prefix() . 'itemable', $item_data);

            // Add deliverables as items
            foreach ($deliverables as $deliverable) {
                $item_data = [
                    'rel_id' => $invoice_id,
                    'rel_type' => 'invoice',
                    'description' => $deliverable['title'] . ' (' . $deliverable['type'] . ' - ' . $deliverable['platform'] . ')',
                    'long_description' => $deliverable['description'] ?? '',
                    'qty' => 1,
                    'rate' => 0, // Already included in main amount
                    'item_order' => $item_order++,
                ];
                $this->db->insert(db_prefix() . 'itemable', $item_data);
            }

            // Update campaign influencer with invoice_id
            $this->db->where('campaign_id', $campaign_id);
            $this->db->where('influencer_id', $influencer_id);
            $this->db->update($this->table_campaign_influencers, ['invoice_id' => $invoice_id]);

            $this->log_campaign_activity($campaign_id, 'Facture créée pour ' . $influencer['firstname'] . ' ' . $influencer['lastname']);

            return $invoice_id;
        }

        return false;
    }

    /**
     * Create quote for influencer
     */
    public function create_quote($campaign_id, $influencer_id)
    {
        $this->load->model('estimates_model');
        $this->load->model('influencers_marketing/influencers_model');

        $campaign = $this->get($campaign_id);
        $influencer = $this->influencers_model->get($influencer_id);

        if (!$campaign || !$influencer) {
            return false;
        }

        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('influencer_id', $influencer_id);
        $campaign_influencer = $this->db->get($this->table_campaign_influencers)->row_array();

        if (!$campaign_influencer) {
            return false;
        }

        // Make sure influencer is converted to customer
        if (!$influencer['customer_id']) {
            $customer_id = $this->influencers_model->convert_to_customer($influencer_id);
        } else {
            $customer_id = $influencer['customer_id'];
        }

        if (!$customer_id) {
            return false;
        }

        // Prepare quote data
        $quote_data = [
            'clientid' => $customer_id,
            'number' => get_option('next_estimate_number'),
            'date' => date('Y-m-d'),
            'expirydate' => date('Y-m-d', strtotime('+30 days')),
            'currency' => $campaign_influencer['currency'],
            'subtotal' => $campaign_influencer['compensation_amount'],
            'total' => $campaign_influencer['compensation_amount'],
            'adminnote' => 'Devis généré pour la campagne : ' . $campaign['name'],
            'clientnote' => 'Proposition de collaboration pour la campagne ' . $campaign['name'],
        ];

        $quote_id = $this->estimates_model->add($quote_data);

        if ($quote_id) {
            // Add quote items
            $item_data = [
                'rel_id' => $quote_id,
                'rel_type' => 'estimate',
                'description' => 'Collaboration campagne : ' . $campaign['name'],
                'long_description' => $campaign['brief'] ?? '',
                'qty' => 1,
                'rate' => $campaign_influencer['compensation_amount'],
                'item_order' => 1,
            ];
            $this->db->insert(db_prefix() . 'itemable', $item_data);

            // Update campaign influencer
            $this->db->where('campaign_id', $campaign_id);
            $this->db->where('influencer_id', $influencer_id);
            $this->db->update($this->table_campaign_influencers, ['quote_id' => $quote_id]);

            $this->log_campaign_activity($campaign_id, 'Devis créé pour ' . $influencer['firstname'] . ' ' . $influencer['lastname']);

            return $quote_id;
        }

        return false;
    }

    /**
     * Create Perfex project from campaign
     */
    public function create_project_from_campaign($campaign_id)
    {
        $this->load->model('projects_model');

        $campaign = $this->get($campaign_id);

        if (!$campaign || $campaign['project_id']) {
            return false;
        }

        $project_data = [
            'name' => $campaign['name'],
            'description' => $campaign['description'] ?? '',
            'clientid' => $campaign['customer_id'] ?? 0,
            'start_date' => $campaign['start_date'] ?? date('Y-m-d'),
            'deadline' => $campaign['end_date'] ?? '',
            'project_cost' => $campaign['budget'],
            'status' => 1, // In progress
            'billing_type' => 1, // Fixed rate
        ];

        $project_id = $this->projects_model->add($project_data);

        if ($project_id) {
            // Update campaign with project_id
            $this->db->where('id', $campaign_id);
            $this->db->update($this->table_campaigns, ['project_id' => $project_id]);

            $this->log_campaign_activity($campaign_id, 'Projet Perfex créé');

            return $project_id;
        }

        return false;
    }

    /**
     * Create task from deliverable
     */
    private function create_task_from_deliverable($deliverable_id)
    {
        $this->load->model('tasks_model');

        $deliverable = $this->get_deliverable($deliverable_id);

        if (!$deliverable) {
            return false;
        }

        $campaign = $this->get($deliverable['campaign_id']);

        $task_data = [
            'name' => $deliverable['title'],
            'description' => $deliverable['description'] ?? '',
            'startdate' => date('Y-m-d'),
            'duedate' => $deliverable['due_date'] ?? '',
            'priority' => 2, // Medium
            'rel_type' => $campaign['project_id'] ? 'project' : null,
            'rel_id' => $campaign['project_id'] ?? null,
            'is_public' => 0,
            'billable' => 0,
            'status' => 1, // Not started
        ];

        $task_id = $this->tasks_model->add($task_data);

        if ($task_id) {
            // Update deliverable with task_id
            $this->db->where('id', $deliverable_id);
            $this->db->update($this->table_deliverables, ['task_id' => $task_id]);

            return $task_id;
        }

        return false;
    }

    /**
     * Update task status based on deliverable status
     */
    private function update_task_status($task_id, $deliverable_status)
    {
        $status_mapping = [
            'pending' => 1,      // Not started
            'in_review' => 4,    // In progress
            'approved' => 4,     // In progress
            'published' => 5,    // Complete
            'rejected' => 1,     // Not started
        ];

        $task_status = $status_mapping[$deliverable_status] ?? 1;

        $this->db->where('id', $task_id);
        $this->db->update(db_prefix() . 'tasks', ['status' => $task_status]);
    }

    /**
     * Get dashboard statistics
     */
    public function get_dashboard_stats()
    {
        $stats = [];

        // Total campaigns
        $stats['total_campaigns'] = $this->db->count_all_results($this->table_campaigns);

        // Active campaigns
        $this->db->where('status', 'active');
        $stats['active_campaigns'] = $this->db->count_all_results($this->table_campaigns);

        // Total budget
        $this->db->select_sum('budget');
        $result = $this->db->get($this->table_campaigns)->row();
        $stats['total_budget'] = $result->budget ?? 0;

        // Total spent
        $this->db->select_sum('actual_cost');
        $result = $this->db->get($this->table_campaigns)->row();
        $stats['total_spent'] = $result->actual_cost ?? 0;

        // Total revenue
        $this->db->select_sum('revenue_generated');
        $result = $this->db->get($this->table_campaigns)->row();
        $stats['total_revenue'] = $result->revenue_generated ?? 0;

        // Average ROI
        if ($stats['total_spent'] > 0) {
            $stats['avg_roi'] = (($stats['total_revenue'] - $stats['total_spent']) / $stats['total_spent']) * 100;
        } else {
            $stats['avg_roi'] = 0;
        }

        // Pending deliverables
        $this->db->where_in('status', ['pending', 'in_review']);
        $stats['pending_deliverables'] = $this->db->count_all_results($this->table_deliverables);

        return $stats;
    }

    /**
     * Log campaign activity
     */
    private function log_campaign_activity($campaign_id, $description)
    {
        $campaign = $this->db->get_where($this->table_campaigns, ['id' => $campaign_id])->row_array();

        if ($campaign) {
            $log_data = [
                'description' => 'Campagne: ' . $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'additional_data' => serialize([
                    'campaign_id' => $campaign_id,
                    'campaign_name' => $campaign['name']
                ]),
            ];

            $this->db->insert(db_prefix() . 'activity_log', $log_data);
        }
    }
}

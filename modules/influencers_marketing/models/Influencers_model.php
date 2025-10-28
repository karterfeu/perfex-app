<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Influencers_model extends App_Model
{
    private $table_influencers;
    private $table_social_accounts;
    private $table_metrics_history;
    private $table_interactions;
    private $table_tags;
    private $table_influencer_tags;

    public function __construct()
    {
        parent::__construct();

        $this->table_influencers = db_prefix() . 'im_influencers';
        $this->table_social_accounts = db_prefix() . 'im_social_accounts';
        $this->table_metrics_history = db_prefix() . 'im_metrics_history';
        $this->table_interactions = db_prefix() . 'im_interactions';
        $this->table_tags = db_prefix() . 'im_tags';
        $this->table_influencer_tags = db_prefix() . 'im_influencer_tags';
    }

    /**
     * Get all influencers with filters
     */
    public function get_influencers($data = [])
    {
        $this->db->select($this->table_influencers . '.*,
            CONCAT(' . $this->table_influencers . '.firstname, " ", ' . $this->table_influencers . '.lastname) as full_name,
            COUNT(DISTINCT sa.id) as social_accounts_count,
            AVG(sa.engagement_rate) as avg_engagement_rate,
            SUM(sa.followers_count) as total_followers');

        $this->db->from($this->table_influencers);
        $this->db->join($this->table_social_accounts . ' sa', 'sa.influencer_id = ' . $this->table_influencers . '.id', 'left');

        // Filters
        if (isset($data['search']) && !empty($data['search'])) {
            $this->db->group_start();
            $this->db->like($this->table_influencers . '.firstname', $data['search']);
            $this->db->or_like($this->table_influencers . '.lastname', $data['search']);
            $this->db->or_like($this->table_influencers . '.email', $data['search']);
            $this->db->or_like($this->table_influencers . '.category', $data['search']);
            $this->db->group_end();
        }

        if (isset($data['status']) && !empty($data['status'])) {
            $this->db->where($this->table_influencers . '.status', $data['status']);
        }

        if (isset($data['category']) && !empty($data['category'])) {
            $this->db->where($this->table_influencers . '.category', $data['category']);
        }

        if (isset($data['staff_id']) && !empty($data['staff_id'])) {
            $this->db->where($this->table_influencers . '.staff_id', $data['staff_id']);
        }

        if (isset($data['min_score']) && !empty($data['min_score'])) {
            $this->db->where($this->table_influencers . '.influence_score >=', $data['min_score']);
        }

        if (isset($data['max_score']) && !empty($data['max_score'])) {
            $this->db->where($this->table_influencers . '.influence_score <=', $data['max_score']);
        }

        if (isset($data['is_favorite']) && $data['is_favorite'] == '1') {
            $this->db->where($this->table_influencers . '.is_favorite', 1);
        }

        if (isset($data['tags']) && !empty($data['tags'])) {
            $tags = is_array($data['tags']) ? $data['tags'] : explode(',', $data['tags']);
            $this->db->join($this->table_influencer_tags . ' it', 'it.influencer_id = ' . $this->table_influencers . '.id', 'inner');
            $this->db->where_in('it.tag_id', $tags);
        }

        $this->db->group_by($this->table_influencers . '.id');

        // Sorting
        if (isset($data['order_by']) && !empty($data['order_by'])) {
            $order_direction = isset($data['order_direction']) ? $data['order_direction'] : 'DESC';
            $this->db->order_by($data['order_by'], $order_direction);
        } else {
            $this->db->order_by($this->table_influencers . '.influence_score', 'DESC');
        }

        // Pagination
        if (isset($data['limit']) && isset($data['offset'])) {
            $this->db->limit($data['limit'], $data['offset']);
        }

        return $this->db->get()->result_array();
    }

    /**
     * Get single influencer by ID
     */
    public function get($id)
    {
        $this->db->where('id', $id);
        $influencer = $this->db->get($this->table_influencers)->row_array();

        if ($influencer) {
            // Get social accounts
            $influencer['social_accounts'] = $this->get_social_accounts($id);

            // Get tags
            $influencer['tags'] = $this->get_influencer_tags($id);

            // Get recent interactions
            $influencer['recent_interactions'] = $this->get_recent_interactions($id, 5);

            // Get staff info if assigned
            if ($influencer['staff_id']) {
                $this->db->select('firstname, lastname, email');
                $this->db->where('staffid', $influencer['staff_id']);
                $influencer['assigned_staff'] = $this->db->get(db_prefix() . 'staff')->row_array();
            }

            // Get customer info if converted
            if ($influencer['customer_id']) {
                $this->db->select('company, phonenumber, website');
                $this->db->where('userid', $influencer['customer_id']);
                $influencer['customer_info'] = $this->db->get(db_prefix() . 'clients')->row_array();
            }
        }

        return $influencer;
    }

    /**
     * Add new influencer
     */
    public function add($data)
    {
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        // Calculate initial influence score if social accounts provided
        if (isset($data['social_accounts']) && !empty($data['social_accounts'])) {
            $social_accounts = $data['social_accounts'];
            unset($data['social_accounts']);
        }

        $this->db->insert($this->table_influencers, $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            // Add social accounts if provided
            if (isset($social_accounts)) {
                foreach ($social_accounts as $account) {
                    $account['influencer_id'] = $insert_id;
                    $this->add_social_account($account);
                }

                // Calculate and update influence score
                $this->calculate_and_update_score($insert_id);
            }

            // Log activity
            $this->log_activity($insert_id, 'Influenceur créé');
        }

        return $insert_id;
    }

    /**
     * Update influencer
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Handle social accounts separately
        if (isset($data['social_accounts'])) {
            $social_accounts = $data['social_accounts'];
            unset($data['social_accounts']);
        }

        $this->db->where('id', $id);
        $result = $this->db->update($this->table_influencers, $data);

        if ($result) {
            // Update social accounts if provided
            if (isset($social_accounts)) {
                foreach ($social_accounts as $account) {
                    if (isset($account['id']) && !empty($account['id'])) {
                        $this->update_social_account($account['id'], $account);
                    } else {
                        $account['influencer_id'] = $id;
                        $this->add_social_account($account);
                    }
                }

                // Recalculate influence score
                $this->calculate_and_update_score($id);
            }

            // Log activity
            $this->log_activity($id, 'Influenceur mis à jour');
        }

        return $result;
    }

    /**
     * Delete influencer
     */
    public function delete($id)
    {
        $influencer = $this->get($id);
        if (!$influencer) {
            return false;
        }

        // Delete related data
        $this->db->where('influencer_id', $id);
        $this->db->delete($this->table_social_accounts);

        $this->db->where('influencer_id', $id);
        $this->db->delete($this->table_interactions);

        $this->db->where('influencer_id', $id);
        $this->db->delete($this->table_influencer_tags);

        // Delete from campaigns
        $this->db->where('influencer_id', $id);
        $this->db->delete(db_prefix() . 'im_campaign_influencers');

        // Delete deliverables
        $this->db->where('influencer_id', $id);
        $this->db->delete(db_prefix() . 'im_deliverables');

        // Delete from content library
        $this->db->where('influencer_id', $id);
        $this->db->delete(db_prefix() . 'im_content_library');

        // Delete influencer
        $this->db->where('id', $id);
        return $this->db->delete($this->table_influencers);
    }

    /**
     * Get social accounts for influencer
     */
    public function get_social_accounts($influencer_id)
    {
        $this->db->where('influencer_id', $influencer_id);
        $this->db->order_by('is_primary', 'DESC');
        $this->db->order_by('followers_count', 'DESC');
        return $this->db->get($this->table_social_accounts)->result_array();
    }

    /**
     * Add social account
     */
    public function add_social_account($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['last_sync'] = date('Y-m-d H:i:s');

        $this->db->insert($this->table_social_accounts, $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            // Save initial metrics to history
            $this->save_metrics_snapshot($insert_id);
        }

        return $insert_id;
    }

    /**
     * Update social account
     */
    public function update_social_account($id, $data)
    {
        $data['last_sync'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $result = $this->db->update($this->table_social_accounts, $data);

        if ($result) {
            // Save metrics snapshot
            $this->save_metrics_snapshot($id);
        }

        return $result;
    }

    /**
     * Delete social account
     */
    public function delete_social_account($id)
    {
        // Delete metrics history
        $this->db->where('social_account_id', $id);
        $this->db->delete($this->table_metrics_history);

        // Delete account
        $this->db->where('id', $id);
        return $this->db->delete($this->table_social_accounts);
    }

    /**
     * Save metrics snapshot to history
     */
    private function save_metrics_snapshot($social_account_id)
    {
        $account = $this->db->get_where($this->table_social_accounts, ['id' => $social_account_id])->row_array();

        if ($account) {
            $today = date('Y-m-d');

            // Check if already saved today
            $exists = $this->db->get_where($this->table_metrics_history, [
                'social_account_id' => $social_account_id,
                'recorded_date' => $today
            ])->row();

            $metrics = [
                'social_account_id' => $social_account_id,
                'followers_count' => $account['followers_count'],
                'following_count' => $account['following_count'],
                'posts_count' => $account['posts_count'],
                'engagement_rate' => $account['engagement_rate'],
                'avg_likes' => $account['avg_likes'],
                'avg_comments' => $account['avg_comments'],
                'avg_views' => $account['avg_views'],
                'recorded_date' => $today,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if ($exists) {
                // Update existing
                $this->db->where('id', $exists->id);
                $this->db->update($this->table_metrics_history, $metrics);
            } else {
                // Insert new
                $this->db->insert($this->table_metrics_history, $metrics);
            }
        }
    }

    /**
     * Calculate influence score (algorithme propriétaire)
     * Score basé sur : engagement, qualité audience, croissance, portée
     */
    public function calculate_influence_score($influencer_id)
    {
        $accounts = $this->get_social_accounts($influencer_id);

        if (empty($accounts)) {
            return 0;
        }

        $total_score = 0;
        $weights = [
            'engagement' => 0.35,    // 35% - Le plus important
            'reach' => 0.25,         // 25% - Portée
            'growth' => 0.20,        // 20% - Croissance
            'quality' => 0.15,       // 15% - Qualité audience
            'activity' => 0.05,      // 5% - Activité régulière
        ];

        foreach ($accounts as $account) {
            $score = 0;

            // 1. Engagement Score (0-100)
            $engagement_rate = $account['engagement_rate'];
            if ($engagement_rate >= 10) {
                $engagement_score = 100;
            } elseif ($engagement_rate >= 5) {
                $engagement_score = 70 + ($engagement_rate - 5) * 6;
            } elseif ($engagement_rate >= 2) {
                $engagement_score = 40 + ($engagement_rate - 2) * 10;
            } else {
                $engagement_score = $engagement_rate * 20;
            }
            $score += $engagement_score * $weights['engagement'];

            // 2. Reach Score (0-100) - Basé sur les followers
            $followers = $account['followers_count'];
            if ($followers >= 1000000) {
                $reach_score = 100;
            } elseif ($followers >= 100000) {
                $reach_score = 70 + (($followers - 100000) / 900000) * 30;
            } elseif ($followers >= 10000) {
                $reach_score = 40 + (($followers - 10000) / 90000) * 30;
            } else {
                $reach_score = ($followers / 10000) * 40;
            }
            $score += $reach_score * $weights['reach'];

            // 3. Growth Score (0-100)
            $growth_rate = $account['growth_rate_30d'];
            if ($growth_rate >= 20) {
                $growth_score = 100;
            } elseif ($growth_rate >= 10) {
                $growth_score = 70 + ($growth_rate - 10) * 3;
            } elseif ($growth_rate >= 5) {
                $growth_score = 50 + ($growth_rate - 5) * 4;
            } elseif ($growth_rate > 0) {
                $growth_score = $growth_rate * 10;
            } else {
                $growth_score = 0;
            }
            $score += $growth_score * $weights['growth'];

            // 4. Audience Quality Score (0-100)
            $fake_followers_score = $this->detect_fake_followers($account['id']);
            $quality_score = 100 - $fake_followers_score; // Inverse du score de faux followers
            $score += $quality_score * $weights['quality'];

            // 5. Activity Score (0-100)
            if ($account['last_post_date']) {
                $days_since_post = (strtotime('now') - strtotime($account['last_post_date'])) / 86400;
                if ($days_since_post <= 1) {
                    $activity_score = 100;
                } elseif ($days_since_post <= 7) {
                    $activity_score = 70 + (7 - $days_since_post) * 5;
                } elseif ($days_since_post <= 30) {
                    $activity_score = 30 + (30 - $days_since_post) / 23 * 40;
                } else {
                    $activity_score = 10;
                }
            } else {
                $activity_score = 50; // Default if no data
            }
            $score += $activity_score * $weights['activity'];

            // Bonus pour compte vérifié
            if ($account['is_verified']) {
                $score += 5;
            }

            // Bonus pour compte principal
            if ($account['is_primary']) {
                $score *= 1.1; // +10%
            }

            $total_score += $score;
        }

        // Average score across all platforms
        $final_score = $total_score / count($accounts);

        // Cap at 100
        $final_score = min(100, $final_score);

        return round($final_score, 2);
    }

    /**
     * Calculate and update influence score
     */
    public function calculate_and_update_score($influencer_id)
    {
        $score = $this->calculate_influence_score($influencer_id);

        $this->db->where('id', $influencer_id);
        $this->db->update($this->table_influencers, [
            'influence_score' => $score,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $score;
    }

    /**
     * Calculate engagement rate for social account
     */
    public function calculate_engagement_rate($social_account_id)
    {
        $account = $this->db->get_where($this->table_social_accounts, ['id' => $social_account_id])->row_array();

        if (!$account || $account['followers_count'] == 0) {
            return 0;
        }

        $total_engagement = $account['avg_likes'] + $account['avg_comments'];
        $engagement_rate = ($total_engagement / $account['followers_count']) * 100;

        return round($engagement_rate, 2);
    }

    /**
     * Detect fake followers (algorithme simplifié)
     * Retourne un score de 0-100 où 100 = beaucoup de faux followers
     */
    public function detect_fake_followers($social_account_id)
    {
        $account = $this->db->get_where($this->table_social_accounts, ['id' => $social_account_id])->row_array();

        if (!$account) {
            return 0;
        }

        $fake_score = 0;

        // 1. Ratio followers/following suspect
        if ($account['following_count'] > 0) {
            $ratio = $account['followers_count'] / $account['following_count'];
            if ($ratio < 0.5) {
                $fake_score += 30; // Suit beaucoup plus qu'il n'a de followers
            } elseif ($ratio < 1) {
                $fake_score += 15;
            }
        }

        // 2. Engagement rate très faible
        $engagement_rate = $account['engagement_rate'];
        if ($engagement_rate < 0.5) {
            $fake_score += 40; // Très faible engagement = suspects
        } elseif ($engagement_rate < 1) {
            $fake_score += 25;
        } elseif ($engagement_rate < 2) {
            $fake_score += 10;
        }

        // 3. Croissance anormalement rapide
        if ($account['growth_rate_30d'] > 50) {
            $fake_score += 20; // Croissance de +50% en 30j est suspect
        } elseif ($account['growth_rate_30d'] > 30) {
            $fake_score += 10;
        }

        // 4. Ratio posts/followers
        if ($account['posts_count'] > 0 && $account['followers_count'] > 0) {
            $posts_per_1k_followers = ($account['posts_count'] / $account['followers_count']) * 1000;
            if ($posts_per_1k_followers < 0.1) {
                $fake_score += 10; // Très peu de posts pour beaucoup de followers
            }
        }

        // Cap at 100
        $fake_score = min(100, $fake_score);

        // Update in database
        $this->db->where('id', $social_account_id);
        $this->db->update($this->table_social_accounts, [
            'audience_quality' => $this->get_quality_label($fake_score)
        ]);

        return $fake_score;
    }

    /**
     * Get quality label from fake followers score
     */
    private function get_quality_label($fake_score)
    {
        if ($fake_score < 20) {
            return 'excellent';
        } elseif ($fake_score < 40) {
            return 'good';
        } elseif ($fake_score < 60) {
            return 'average';
        } else {
            return 'poor';
        }
    }

    /**
     * Get metrics history for social account
     */
    public function get_metrics_history($social_account_id, $days = 30)
    {
        $start_date = date('Y-m-d', strtotime("-{$days} days"));

        $this->db->where('social_account_id', $social_account_id);
        $this->db->where('recorded_date >=', $start_date);
        $this->db->order_by('recorded_date', 'ASC');

        return $this->db->get($this->table_metrics_history)->result_array();
    }

    /**
     * Get influencer tags
     */
    public function get_influencer_tags($influencer_id)
    {
        $this->db->select('t.*');
        $this->db->from($this->table_tags . ' t');
        $this->db->join($this->table_influencer_tags . ' it', 'it.tag_id = t.id');
        $this->db->where('it.influencer_id', $influencer_id);

        return $this->db->get()->result_array();
    }

    /**
     * Add tag to influencer
     */
    public function add_tag($influencer_id, $tag_id)
    {
        $data = [
            'influencer_id' => $influencer_id,
            'tag_id' => $tag_id,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Check if already exists
        $exists = $this->db->get_where($this->table_influencer_tags, [
            'influencer_id' => $influencer_id,
            'tag_id' => $tag_id
        ])->row();

        if (!$exists) {
            $this->db->insert($this->table_influencer_tags, $data);
            return true;
        }

        return false;
    }

    /**
     * Remove tag from influencer
     */
    public function remove_tag($influencer_id, $tag_id)
    {
        $this->db->where('influencer_id', $influencer_id);
        $this->db->where('tag_id', $tag_id);
        return $this->db->delete($this->table_influencer_tags);
    }

    /**
     * Get all available tags
     */
    public function get_all_tags()
    {
        $this->db->order_by('name', 'ASC');
        return $this->db->get($this->table_tags)->result_array();
    }

    /**
     * Create new tag
     */
    public function create_tag($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_tags, $data);
        return $this->db->insert_id();
    }

    /**
     * Get recent interactions
     */
    public function get_recent_interactions($influencer_id, $limit = 10)
    {
        $this->db->select($this->table_interactions . '.*,
            CONCAT(s.firstname, " ", s.lastname) as staff_name');
        $this->db->from($this->table_interactions);
        $this->db->join(db_prefix() . 'staff s', 's.staffid = ' . $this->table_interactions . '.staff_id', 'left');
        $this->db->where($this->table_interactions . '.influencer_id', $influencer_id);
        $this->db->order_by($this->table_interactions . '.contact_date', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    /**
     * Add interaction
     */
    public function add_interaction($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['staff_id'] = $data['staff_id'] ?? get_staff_user_id();

        $this->db->insert($this->table_interactions, $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $this->log_activity($data['influencer_id'], 'Nouvelle interaction : ' . $data['type']);
        }

        return $insert_id;
    }

    /**
     * Convert influencer to Perfex customer
     */
    public function convert_to_customer($influencer_id)
    {
        $influencer = $this->get($influencer_id);

        if (!$influencer || $influencer['customer_id']) {
            return false; // Already converted or not found
        }

        $this->load->model('clients_model');

        // Prepare customer data
        $customer_data = [
            'company' => $influencer['full_name'] ?? ($influencer['firstname'] . ' ' . $influencer['lastname']),
            'vat' => '',
            'phonenumber' => $influencer['phone'] ?? '',
            'country' => $influencer['country'] ?? '',
            'city' => '',
            'zip' => '',
            'state' => '',
            'address' => '',
            'website' => '',
            'billing_street' => '',
            'billing_city' => '',
            'billing_state' => '',
            'billing_zip' => '',
            'billing_country' => $influencer['country'] ?? '',
        ];

        $customer_id = $this->clients_model->add($customer_data);

        if ($customer_id) {
            // Add contact
            $contact_data = [
                'customer_id' => $customer_id,
                'firstname' => $influencer['firstname'],
                'lastname' => $influencer['lastname'],
                'email' => $influencer['email'],
                'phonenumber' => $influencer['phone'] ?? '',
                'title' => 'Influenceur',
                'password' => '',
                'send_set_password_email' => 0,
                'donotsendwelcomeemail' => 1,
                'is_primary' => 1,
            ];

            $this->load->model('clients_model');
            $contact_id = $this->clients_model->add_contact($contact_data, $customer_id);

            // Update influencer with customer_id and contact_id
            $this->db->where('id', $influencer_id);
            $this->db->update($this->table_influencers, [
                'customer_id' => $customer_id,
                'contact_id' => $contact_id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $this->log_activity($influencer_id, 'Converti en client Perfex CRM');

            return $customer_id;
        }

        return false;
    }

    /**
     * Get dashboard statistics
     */
    public function get_dashboard_stats()
    {
        $stats = [];

        // Total influencers
        $stats['total_influencers'] = $this->db->count_all_results($this->table_influencers);

        // By status
        $this->db->select('status, COUNT(*) as count');
        $this->db->group_by('status');
        $stats['by_status'] = $this->db->get($this->table_influencers)->result_array();

        // Average influence score
        $this->db->select_avg('influence_score');
        $result = $this->db->get($this->table_influencers)->row();
        $stats['avg_influence_score'] = round($result->influence_score ?? 0, 2);

        // Total followers across all platforms
        $this->db->select_sum('followers_count');
        $result = $this->db->get($this->table_social_accounts)->row();
        $stats['total_followers'] = $result->followers_count ?? 0;

        // Average engagement rate
        $this->db->select_avg('engagement_rate');
        $result = $this->db->get($this->table_social_accounts)->row();
        $stats['avg_engagement_rate'] = round($result->engagement_rate ?? 0, 2);

        // Top influencers
        $this->db->select('id, firstname, lastname, influence_score, profile_picture');
        $this->db->order_by('influence_score', 'DESC');
        $this->db->limit(5);
        $stats['top_influencers'] = $this->db->get($this->table_influencers)->result_array();

        // Recent additions
        $this->db->select('COUNT(*) as count');
        $this->db->where('created_at >=', date('Y-m-d', strtotime('-30 days')));
        $result = $this->db->get($this->table_influencers)->row();
        $stats['new_last_30_days'] = $result->count ?? 0;

        return $stats;
    }

    /**
     * Search influencers (autocomplete)
     */
    public function search($query, $limit = 10)
    {
        $this->db->select('id, firstname, lastname, email, profile_picture, influence_score');
        $this->db->group_start();
        $this->db->like('firstname', $query);
        $this->db->or_like('lastname', $query);
        $this->db->or_like('email', $query);
        $this->db->group_end();
        $this->db->limit($limit);
        $this->db->order_by('influence_score', 'DESC');

        return $this->db->get($this->table_influencers)->result_array();
    }

    /**
     * Log activity
     */
    private function log_activity($influencer_id, $description)
    {
        $influencer = $this->get($influencer_id);
        if ($influencer) {
            $log_data = [
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id(),
                'additional_data' => serialize([
                    'influencer_id' => $influencer_id,
                    'influencer_name' => $influencer['firstname'] . ' ' . $influencer['lastname']
                ]),
            ];

            $this->db->insert(db_prefix() . 'activity_log', $log_data);
        }
    }

    /**
     * Toggle favorite
     */
    public function toggle_favorite($influencer_id)
    {
        $influencer = $this->get($influencer_id);
        if ($influencer) {
            $new_value = $influencer['is_favorite'] == 1 ? 0 : 1;
            $this->db->where('id', $influencer_id);
            $this->db->update($this->table_influencers, ['is_favorite' => $new_value]);
            return $new_value;
        }
        return false;
    }

    /**
     * Get recommended influencers for campaign
     */
    public function get_recommendations($criteria)
    {
        $this->db->select($this->table_influencers . '.*,
            AVG(sa.engagement_rate) as avg_engagement,
            SUM(sa.followers_count) as total_reach');
        $this->db->from($this->table_influencers);
        $this->db->join($this->table_social_accounts . ' sa', 'sa.influencer_id = ' . $this->table_influencers . '.id', 'left');

        // Apply criteria
        if (isset($criteria['min_score'])) {
            $this->db->where($this->table_influencers . '.influence_score >=', $criteria['min_score']);
        }

        if (isset($criteria['category'])) {
            $this->db->where($this->table_influencers . '.category', $criteria['category']);
        }

        if (isset($criteria['min_followers'])) {
            $this->db->having('total_reach >=', $criteria['min_followers']);
        }

        if (isset($criteria['location'])) {
            $this->db->where($this->table_influencers . '.country', $criteria['location']);
        }

        $this->db->group_by($this->table_influencers . '.id');
        $this->db->order_by($this->table_influencers . '.influence_score', 'DESC');
        $this->db->limit(isset($criteria['limit']) ? $criteria['limit'] : 10);

        return $this->db->get()->result_array();
    }
}

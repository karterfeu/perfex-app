/**
 * Influencers Marketing Module - Main JavaScript
 */

(function() {
    'use strict';

    // Initialize on document ready
    $(document).ready(function() {
        initInfluencersModule();
    });

    function initInfluencersModule() {
        initSearch();
        initFilters();
        initViewSwitcher();
        initModals();
        initForms();
        initDragDrop();
        initBulkActions();
        initTooltips();
        initInfiniteScroll();
    }

    /**
     * Search functionality with debounce
     */
    function initSearch() {
        let searchTimeout;
        $('.im-search-input').on('input', function() {
            clearTimeout(searchTimeout);
            const query = $(this).val();

            searchTimeout = setTimeout(function() {
                if (query.length >= 2) {
                    performSearch(query);
                } else if (query.length === 0) {
                    resetSearch();
                }
            }, 500);
        });
    }

    function performSearch(query) {
        const $container = $('.im-influencers-grid, .im-table-container');

        $.ajax({
            url: admin_url + 'influencers_marketing/search',
            method: 'GET',
            data: { q: query },
            success: function(results) {
                updateSearchResults(results);
            }
        });
    }

    function resetSearch() {
        location.reload();
    }

    function updateSearchResults(results) {
        const $grid = $('.im-influencers-grid');
        $grid.empty();

        if (results.length === 0) {
            $grid.html('<div class="im-no-results">Aucun influenceur trouvé</div>');
            return;
        }

        results.forEach(function(influencer) {
            const card = createInfluencerCard(influencer);
            $grid.append(card);
        });
    }

    /**
     * Filters
     */
    function initFilters() {
        $('.im-filter-select').on('change', function() {
            applyFilters();
        });

        $('#im-filter-min-score, #im-filter-max-score').on('change', function() {
            applyFilters();
        });
    }

    function applyFilters() {
        const filters = {
            status: $('#im-filter-status').val(),
            category: $('#im-filter-category').val(),
            staff_id: $('#im-filter-staff').val(),
            min_score: $('#im-filter-min-score').val(),
            max_score: $('#im-filter-max-score').val(),
            tags: $('#im-filter-tags').val()
        };

        const queryString = $.param(filters);
        window.location.href = window.location.pathname + '?' + queryString;
    }

    /**
     * View switcher (Grid/List/Map)
     */
    function initViewSwitcher() {
        $('.im-view-btn').on('click', function() {
            const view = $(this).data('view');

            $('.im-view-btn').removeClass('active');
            $(this).addClass('active');

            switchView(view);

            // Save preference
            localStorage.setItem('im_preferred_view', view);
        });

        // Load saved preference
        const savedView = localStorage.getItem('im_preferred_view');
        if (savedView) {
            $(`.im-view-btn[data-view="${savedView}"]`).click();
        }
    }

    function switchView(view) {
        const $container = $('#im-influencers-container');

        switch(view) {
            case 'grid':
                $container.removeClass('im-list-view im-map-view').addClass('im-grid-view');
                break;
            case 'list':
                $container.removeClass('im-grid-view im-map-view').addClass('im-list-view');
                break;
            case 'map':
                $container.removeClass('im-grid-view im-list-view').addClass('im-map-view');
                initMap();
                break;
        }
    }

    /**
     * Modals
     */
    function initModals() {
        // Open modal
        $('[data-toggle="im-modal"]').on('click', function(e) {
            e.preventDefault();
            const target = $(this).data('target');
            openModal(target);
        });

        // Close modal
        $('.im-modal-close, .im-modal-backdrop').on('click', function() {
            closeAllModals();
        });

        // Close on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAllModals();
            }
        });
    }

    function openModal(modalId) {
        $(modalId).addClass('active').addClass('im-fade-in');
        $('.im-modal-backdrop').addClass('active');
        $('body').css('overflow', 'hidden');
    }

    function closeAllModals() {
        $('.im-modal').removeClass('active');
        $('.im-modal-backdrop').removeClass('active');
        $('body').css('overflow', '');
    }

    /**
     * Forms
     */
    function initForms() {
        // Social account form
        $('#im-add-social-account-form').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert_float('success', 'Compte social ajouté avec succès');
                        location.reload();
                    }
                },
                error: function() {
                    alert_float('danger', 'Une erreur est survenue');
                }
            });
        });

        // Auto-calculate engagement rate
        $('#im-followers, #im-avg-likes, #im-avg-comments').on('input', function() {
            calculateEngagementRate();
        });

        // Platform-specific fields
        $('#im-platform').on('change', function() {
            showPlatformSpecificFields($(this).val());
        });
    }

    function calculateEngagementRate() {
        const followers = parseInt($('#im-followers').val()) || 0;
        const avgLikes = parseInt($('#im-avg-likes').val()) || 0;
        const avgComments = parseInt($('#im-avg-comments').val()) || 0;

        if (followers > 0) {
            const engagement = ((avgLikes + avgComments) / followers) * 100;
            $('#im-engagement-rate').val(engagement.toFixed(2));
        }
    }

    function showPlatformSpecificFields(platform) {
        $('.im-platform-specific').hide();
        $(`.im-platform-${platform}`).show();
    }

    /**
     * Drag and drop for Kanban view
     */
    function initDragDrop() {
        if ($('.im-kanban-board').length === 0) return;

        $('.im-kanban-column').sortable({
            connectWith: '.im-kanban-column',
            cursor: 'move',
            placeholder: 'im-kanban-placeholder',
            tolerance: 'pointer',
            update: function(event, ui) {
                const influencerId = ui.item.data('influencer-id');
                const newStatus = ui.item.closest('.im-kanban-column').data('status');

                updateInfluencerStatus(influencerId, newStatus);
            }
        });
    }

    function updateInfluencerStatus(influencerId, status) {
        $.ajax({
            url: admin_url + 'influencers_marketing/update_status',
            method: 'POST',
            data: {
                influencer_id: influencerId,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    alert_float('success', 'Statut mis à jour');
                }
            }
        });
    }

    /**
     * Bulk actions
     */
    function initBulkActions() {
        // Select all checkbox
        $('#im-select-all').on('change', function() {
            $('.im-select-item').prop('checked', $(this).prop('checked'));
            updateBulkActionsUI();
        });

        // Individual checkboxes
        $('.im-select-item').on('change', function() {
            updateBulkActionsUI();
        });

        // Bulk action button
        $('#im-bulk-action-btn').on('click', function() {
            const action = $('#im-bulk-action').val();
            const selectedIds = getSelectedIds();

            if (selectedIds.length === 0) {
                alert_float('warning', 'Veuillez sélectionner au moins un élément');
                return;
            }

            if (confirm(`Êtes-vous sûr de vouloir ${action} ${selectedIds.length} élément(s) ?`)) {
                performBulkAction(action, selectedIds);
            }
        });
    }

    function updateBulkActionsUI() {
        const selectedCount = $('.im-select-item:checked').length;
        const $bulkActions = $('#im-bulk-actions');

        if (selectedCount > 0) {
            $bulkActions.show();
            $('#im-selected-count').text(selectedCount);
        } else {
            $bulkActions.hide();
        }
    }

    function getSelectedIds() {
        const ids = [];
        $('.im-select-item:checked').each(function() {
            ids.push($(this).val());
        });
        return ids;
    }

    function performBulkAction(action, ids) {
        const additionalData = {};

        // Get additional data based on action
        if (action === 'update_status') {
            additionalData.status = prompt('Nouveau statut (prospect/contacted/negotiating/active/inactive):');
            if (!additionalData.status) return;
        } else if (action === 'assign_staff') {
            additionalData.staff_id = prompt('ID du commercial:');
            if (!additionalData.staff_id) return;
        }

        $.ajax({
            url: admin_url + 'influencers_marketing/bulk_action',
            method: 'POST',
            data: {
                action: action,
                ids: ids,
                ...additionalData
            },
            success: function(response) {
                if (response.success) {
                    alert_float('success', `${response.count} élément(s) mis à jour`);
                    location.reload();
                } else {
                    alert_float('danger', 'Une erreur est survenue');
                }
            }
        });
    }

    /**
     * Tooltips
     */
    function initTooltips() {
        $('[data-toggle="tooltip"]').tooltip();
    }

    /**
     * Infinite scroll
     */
    function initInfiniteScroll() {
        if ($('#im-load-more').length === 0) return;

        let page = 1;
        let loading = false;

        $(window).on('scroll', function() {
            if (loading) return;

            const scrollTop = $(window).scrollTop();
            const windowHeight = $(window).height();
            const documentHeight = $(document).height();

            if (scrollTop + windowHeight >= documentHeight - 500) {
                loadMoreInfluencers();
            }
        });

        function loadMoreInfluencers() {
            loading = true;
            page++;

            $.ajax({
                url: admin_url + 'influencers_marketing/load_more',
                method: 'GET',
                data: {
                    page: page,
                    limit: 12
                },
                success: function(results) {
                    if (results.length > 0) {
                        appendInfluencers(results);
                        loading = false;
                    } else {
                        $('#im-load-more').hide();
                    }
                }
            });
        }

        function appendInfluencers(influencers) {
            const $grid = $('.im-influencers-grid');

            influencers.forEach(function(influencer) {
                const card = createInfluencerCard(influencer);
                $grid.append(card);
            });
        }
    }

    /**
     * Create influencer card HTML
     */
    function createInfluencerCard(influencer) {
        const avatar = influencer.profile_picture
            ? `<img src="${influencer.profile_picture}" class="im-influencer-avatar" alt="${influencer.firstname}">`
            : `<div class="im-influencer-avatar-placeholder">${influencer.firstname.charAt(0)}${influencer.lastname.charAt(0)}</div>`;

        const scoreClass = getScoreClass(influencer.influence_score);

        return `
            <div class="im-influencer-card im-fade-in" data-influencer-id="${influencer.id}">
                <div class="im-score-badge ${scoreClass}">${influencer.influence_score}</div>
                <div class="im-influencer-header">
                    ${avatar}
                    <div class="im-influencer-info">
                        <h3>${influencer.firstname} ${influencer.lastname}</h3>
                        <div class="im-influencer-category">${influencer.category || 'Non spécifié'}</div>
                    </div>
                </div>
                <div class="im-stats-row">
                    <div class="im-stat-item">
                        <div class="im-stat-item-label">Followers</div>
                        <div class="im-stat-item-value">${formatNumber(influencer.total_followers || 0)}</div>
                    </div>
                    <div class="im-stat-item">
                        <div class="im-stat-item-label">Engagement</div>
                        <div class="im-stat-item-value">${influencer.avg_engagement_rate || 0}%</div>
                    </div>
                </div>
                <div class="im-influencer-actions">
                    <a href="${admin_url}influencers_marketing/influencer/${influencer.id}" class="im-btn im-btn-primary">
                        <i class="fa fa-eye"></i> Voir profil
                    </a>
                </div>
            </div>
        `;
    }

    /**
     * Helper functions
     */
    function getScoreClass(score) {
        if (score >= 80) return 'excellent';
        if (score >= 60) return 'good';
        if (score >= 40) return 'average';
        return 'poor';
    }

    function formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }

    /**
     * Toggle favorite
     */
    $(document).on('click', '.im-toggle-favorite', function(e) {
        e.preventDefault();
        const influencerId = $(this).data('influencer-id');
        const $icon = $(this).find('i');

        $.ajax({
            url: admin_url + 'influencers_marketing/toggle_favorite/' + influencerId,
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    if (response.is_favorite) {
                        $icon.removeClass('fa-star-o').addClass('fa-star');
                    } else {
                        $icon.removeClass('fa-star').addClass('fa-star-o');
                    }
                }
            }
        });
    });

    /**
     * Delete confirmation
     */
    $(document).on('click', '.im-delete-btn', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const name = $(this).data('name') || 'cet élément';

        if (confirm(`Êtes-vous sûr de vouloir supprimer ${name} ?`)) {
            $.ajax({
                url: url,
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        alert_float('success', 'Supprimé avec succès');
                        location.reload();
                    } else {
                        alert_float('danger', 'Erreur lors de la suppression');
                    }
                }
            });
        }
    });

    /**
     * Map initialization (for location view)
     */
    function initMap() {
        if ($('#im-map-container').length === 0) return;

        // Initialize map (example with Leaflet)
        // This would require including Leaflet library
        // const map = L.map('im-map-container').setView([48.8566, 2.3522], 5);
        // Add markers for each influencer location
    }

    // Export functions for external use
    window.InfluencersMarketing = {
        openModal: openModal,
        closeAllModals: closeAllModals,
        createInfluencerCard: createInfluencerCard,
        formatNumber: formatNumber
    };

})();

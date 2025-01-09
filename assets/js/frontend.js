jQuery(document).ready(function($) {
    // Roster Filters
    $('.ebm-filter').on('change', function() {
        const position = $('[data-filter="position"]').val();
        const classYear = $('[data-filter="class"]').val();
        
        $('.ebm-player-card').each(function() {
            const card = $(this);
            const playerPosition = card.data('position');
            const playerClass = card.data('class');
            
            const positionMatch = !position || playerPosition === position;
            const classMatch = !classYear || playerClass === classYear;
            
            if (positionMatch && classMatch) {
                card.show();
            } else {
                card.hide();
            }
        });
    });

    // Stats Charts
    if ($('.ebm-stats-chart').length) {
        $('.ebm-stats-chart').each(function() {
            const canvas = this;
            const ctx = canvas.getContext('2d');
            const type = $(canvas).data('chart-type');
            const data = JSON.parse($(canvas).data('chart-data'));
            
            new Chart(ctx, {
                type: type,
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    }

    // Player Profile Tabs
    $('.ebm-profile-tab').on('click', function(e) {
        e.preventDefault();
        
        const tab = $(this);
        const target = tab.data('target');
        
        $('.ebm-profile-tab').removeClass('active');
        tab.addClass('active');
        
        $('.ebm-profile-content').hide();
        $(target).show();
    });

    // Stats Toggle
    $('.ebm-stats-toggle').on('click', function() {
        const button = $(this);
        const target = button.data('target');
        const text = button.data('text');
        const altText = button.data('alt-text');
        
        $(target).slideToggle();
        button.text(button.text() === text ? altText : text);
    });

    // Career Stats Calculator
    function calculateCareerStats() {
        const stats = {
            games: 0,
            points: 0,
            rebounds: 0,
            assists: 0,
            steals: 0,
            blocks: 0,
            fgMade: 0,
            fgAttempted: 0,
            threeMade: 0,
            threeAttempted: 0,
            ftMade: 0,
            ftAttempted: 0
        };
        
        $('.ebm-game-stat').each(function() {
            const row = $(this);
            stats.games++;
            stats.points += parseInt(row.data('points')) || 0;
            stats.rebounds += parseInt(row.data('rebounds')) || 0;
            stats.assists += parseInt(row.data('assists')) || 0;
            stats.steals += parseInt(row.data('steals')) || 0;
            stats.blocks += parseInt(row.data('blocks')) || 0;
            stats.fgMade += parseInt(row.data('fg-made')) || 0;
            stats.fgAttempted += parseInt(row.data('fg-attempted')) || 0;
            stats.threeMade += parseInt(row.data('three-made')) || 0;
            stats.threeAttempted += parseInt(row.data('three-attempted')) || 0;
            stats.ftMade += parseInt(row.data('ft-made')) || 0;
            stats.ftAttempted += parseInt(row.data('ft-attempted')) || 0;
        });

        // Calculate averages
        if (stats.games > 0) {
            $('.ebm-career-ppg').text((stats.points / stats.games).toFixed(1));
            $('.ebm-career-rpg').text((stats.rebounds / stats.games).toFixed(1));
            $('.ebm-career-apg').text((stats.assists / stats.games).toFixed(1));
            $('.ebm-career-spg').text((stats.steals / stats.games).toFixed(1));
            $('.ebm-career-bpg').text((stats.blocks / stats.games).toFixed(1));
        }

        // Calculate percentages
        if (stats.fgAttempted > 0) {
            $('.ebm-career-fg').text(((stats.fgMade / stats.fgAttempted) * 100).toFixed(1) + '%');
        }
        if (stats.threeAttempted > 0) {
            $('.ebm-career-three').text(((stats.threeMade / stats.threeAttempted) * 100).toFixed(1) + '%');
        }
        if (stats.ftAttempted > 0) {
            $('.ebm-career-ft').text(((stats.ftMade / stats.ftAttempted) * 100).toFixed(1) + '%');
        }
    }

    if ($('.ebm-game-stat').length) {
        calculateCareerStats();
    }

    // Print Stats
    $('.ebm-print-stats').on('click', function(e) {
        e.preventDefault();
        window.print();
    });

    // Export Stats
    $('.ebm-export-stats').on('click', function(e) {
        e.preventDefault();
        
        const stats = [];
        const headers = [];
        
        // Get headers
        $('.ebm-stats-table th').each(function() {
            headers.push($(this).text().trim());
        });
        
        // Get data
        $('.ebm-stats-table tbody tr').each(function() {
            const row = {};
            $(this).find('td').each(function(index) {
                row[headers[index]] = $(this).text().trim();
            });
            stats.push(row);
        });
        
        // Create CSV
        let csv = headers.join(',') + '\n';
        stats.forEach(function(row) {
            csv += headers.map(header => row[header]).join(',') + '\n';
        });
        
        // Download
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.setAttribute('hidden', '');
        a.setAttribute('href', url);
        a.setAttribute('download', 'stats.csv');
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
});
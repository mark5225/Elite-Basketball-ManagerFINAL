# Elite Basketball Manager

A comprehensive WordPress plugin for managing basketball teams, players, and statistics.

## Features

- Team Management
- Player Profiles
- Game Statistics
- Recruiting Management
- Custom Widgets
- Shortcodes
- Interactive Dashboard

## Installation

1. Upload the plugin files to `/wp-content/plugins/elite-basketball-manager`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings under 'Basketball Manager' in the admin menu

## Usage

### Shortcodes

- `[ebm_roster team_id="123"]` - Display team roster
- `[ebm_player_stats player_id="456"]` - Display player statistics
- `[ebm_team_stats team_id="123"]` - Display team statistics
- `[ebm_recruitment_stats]` - Display recruitment statistics

### Widgets

- Team Roster Widget
- Player Stats Widget
- Recruiting Commitments Widget

## Development

### Requirements

- PHP 7.4+
- WordPress 5.8+
- MySQL 5.7+

### Database Structure

The plugin creates the following custom tables:

- `wp_ebm_game_stats`
- `wp_ebm_recruitment`
- `wp_ebm_recruiting_interactions`

### Post Types

- `ebm_player`
- `ebm_team`
- `ebm_game`

### Taxonomies

- `ebm_position`
- `ebm_team_category`

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

GPL v2 or later
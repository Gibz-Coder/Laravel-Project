# Endtime Dashboard Documentation

This comprehensive document explains the Endtime Dashboard implementation, including frontend components, backend architecture, data flow, and recent performance optimizations.

## Overview

The Endtime Dashboard is a real-time monitoring system for tracking machine endtime and submitted lots in Visual Inspection process. It provides visual representations of production targets, achievements, and performance metrics across different production lines, cutoff periods, and lot types.

## Architecture

### Frontend Components

The dashboard is built using:
- **Laravel Livewire**: For reactive UI components with server-side rendering
- **Bootstrap 5**: For responsive layout and UI components
- **ApexCharts**: For interactive data visualizations
- **SweetAlert2**: For toast notifications and alerts
- **Custom JavaScript**: For enhanced interactivity and real-time updates

### Backend Components

- **Laravel Controllers**: Handle HTTP requests and responses
- **Livewire Components**: Manage state and provide real-time updates
- **Service Classes**: Encapsulate business logic and data processing
- **Models**: Represent database tables and relationships
- **Database**: Stores production data, targets, and achievements

### Data Flow

1. User interacts with the dashboard (changes filters, toggles auto-refresh)
2. Livewire components capture these interactions
3. Backend processes the requests and queries the database
4. Results are returned to the Livewire components
5. Components re-render with the updated data
6. JavaScript enhances the UI with charts and notifications

## Performance Optimizations

The dashboard has been optimized to address performance issues:

### Previous Issues
- Slow refresh times (20+ seconds) when using Livewire's built-in refresh mechanism
- Multiple nested components (11 total) each making separate database queries
- Unnecessary refreshes when toggling auto-refresh OFF

### Implemented Solutions
- **Page Reload vs. Livewire Refresh**: Filter changes now trigger a full page reload (3-5 seconds) instead of Livewire's slower refresh mechanism
- **Selective Auto-Refresh**: Auto-refresh toggle only triggers a refresh when turned ON, not when turned OFF
- **Loading Indicator**: Added a full-screen loading overlay during page reloads
- **Optimized Event Handling**: Streamlined event propagation between components

## Dashboard Components

The dashboard consists of 11 Livewire components:

1. **Main Dashboard** (`EndtimeDashboard.php`): Controls filters and dispatches events
2. **Target Card** (`TargetCard.php`): Displays production targets
3. **Endtime Card** (`EndtimeCard.php`): Shows forecasted endtime lots
4. **Submitted Card** (`SubmittedCard.php`): Displays submitted lots
5. **Remaining Card** (`RemainingCard.php`): Shows remaining lots to be processed
6. **Performance Stats** (`PerformanceStats.php`): Displays key performance indicators
7. **Line Performance Chart** (`LinePerformanceChart.php`): Visualizes performance by production line
8. **Progress Chart** (`ProgressChart.php`): Shows overall progress as a donut chart
9. **Line Achievement Table** (`LineAchievementTable.php`): Detailed achievement data by line
10. **Size Achievement Table** (`SizeAchievementTable.php`): Achievement data by chip size
11. **Submitted Per Cutoff Table** (`SubmittedPerCutoffTable.php`): Submitted lots by cutoff period

## Database Structure

The dashboard interacts with several database tables:

1. **vi_capa_ref**: Contains target capacity data by line, date, and cutoff
2. **vi_endtime_forecast**: Stores forecasted endtime lots
3. **vi_submitted_lots**: Records submitted lot information
4. **vi_prod_wip_realtime**: Contains real-time work-in-progress data

## User Interface Controls

### 1. Auto Refresh Toggle Button

**Implementation:**
- Uses `wire:model="autoRefresh"` to bind to the `$autoRefresh` property
- When toggled, calls `updateAutoRefreshState()` which:
  - Saves the state to the session
  - If turned ON, updates to the current date/time and reloads the page
  - Enables automatic polling via `wire:poll.{{ $refreshInterval }}s`

**How it works:**
- When ON: The dashboard automatically refreshes every 5 minutes (300 seconds)
- When OFF: No automatic refreshing occurs
- Only triggers a page reload when toggled from OFF to ON

### 2. Manual Refresh Button

**Implementation:**
- Uses `onclick="window.location.reload()"` for a full page reload
- Shows the loading overlay during refresh

**How it works:**
- When clicked, it performs a full browser page reload
- The loading overlay appears during the reload process

### 3. Date Picker

**Implementation:**
- Uses a hidden input with `wire:model="date"` and `wire:change="updateDate($event.target.value)"`
- Shows a button that triggers the browser's native date picker
- Displays the selected date in Manila timezone format

**How it works:**
- When a date is selected, it:
  - Updates the `$date` property
  - Stores the date in session
  - Triggers a full page reload

### 4. WorkType Button (Dropdown)

**Implementation:**
- Default is set to "all" in the Livewire component
- Uses dropdown items with `wire:click="updateWorktype('value')"`
- Shows the current selection in the button text

**How it works:**
- When an option is selected, it:
  - Updates the `$worktype` property
  - Stores the worktype in session
  - Triggers a full page reload

### 5. LotType Button (Dropdown)

**Implementation:**
- Default is set to "all" in the Livewire component
- Uses dropdown items with `wire:click="updateLottype('value')"`
- Shows the current selection in the button text

**How it works:**
- When an option is selected, it:
  - Updates the `$lottype` property
  - Stores the lottype in session
  - Triggers a full page reload

### 6. Cutoff Selection Buttons

**Implementation:**
- Uses a button group with dynamic styling based on the selected cutoff
- Each button has `wire:click="updateCutoff('value')"`
- The active button is highlighted with the `btn-primary` class

**How it works:**
- When a cutoff period is selected, it:
  - Updates the `$cutoff` property
  - Stores the cutoff in session
  - Triggers a full page reload
- When auto-refresh is enabled, it automatically selects the current cutoff based on Manila time

### 7. Add Endtime Button

**Implementation:**
- Opens a modal with a form for adding endtime forecasted lots
- Uses JavaScript to handle lot lookup and data validation
- Dynamically generates date and cutoff options based on current time

**How it works:**
- When a lot number is entered, it:
  - Looks up lot details from the database
  - Populates model ID, quantity, and area fields
  - Allows selection of date, cutoff, and lot type
  - Saves the data to the database when submitted

### 8. Add Submitted Button

**Implementation:**
- Opens a modal with a form for adding submitted lots
- Similar to the Add Endtime modal but with cutoff fixed to the current period
- Uses JavaScript for lot lookup and validation

**How it works:**
- When a lot number is entered, it:
  - Looks up lot details from the database
  - Populates model ID, quantity, and area fields
  - Uses the current date and cutoff period
  - Saves the data to the database when submitted

### 9. Update WIP Modal Button

**Implementation:**
- Opens a modal with a textarea for raw WIP data input
- The textarea uses `wire:model="rawWipData"` to bind to the component
- The process button uses `wire:click="processWipData"`

**How it works:**
- When the modal is opened, you can paste raw WIP data
- When the process button is clicked, it:
  - Validates the input format
  - Processes the data and updates the database
  - Shows success/error notifications
  - Refreshes the dashboard data if successful

### 10. Export Button

**Implementation:**
- Uses a separate Livewire component (`EndtimeDashboard.ExportDateRange`)
- The button toggles the visibility of a date range picker
- The date range picker uses Flatpickr for date selection

**How it works:**
- When clicked, it toggles the date range picker
- When dates are selected and export is clicked, it:
  - Adjusts the dates (adds one day to compensate for date shift)
  - Redirects to the export endpoint with the selected date range
  - Generates an Excel file with the dashboard data

## Backend Implementation

### Main Component (EndtimeDashboard.php)

The main Livewire component handles:
- Filter state management (date, cutoff, worktype, lottype)
- Auto-refresh functionality
- Event dispatching to child components
- Session management for persistence
- Timezone handling for Manila time

Key methods:
- `mount()`: Initializes the component with session data or defaults
- `updateDate()`: Handles date changes and triggers page reload
- `updateCutoff()`: Handles cutoff changes and triggers page reload
- `updateWorktype()`: Handles worktype changes and triggers page reload
- `updateLottype()`: Handles lottype changes and triggers page reload
- `updateAutoRefreshState()`: Manages auto-refresh toggle state
- `loadData()`: Refreshes all dashboard data
- `getCurrentCutoff()`: Determines the current cutoff based on Manila time
- `processWipData()`: Processes raw WIP data from the update modal

### Child Components

Each child component:
- Listens for events from the main component
- Retrieves filter values from the session
- Queries the database based on the current filters
- Renders its specific part of the dashboard
- Shows loading indicators during data fetching
- Handles errors gracefully

### Service Classes

The dashboard uses service classes to encapsulate business logic:
- `EndtimeDashboardService`: Provides methods for retrieving dashboard data
- `WipDataProcessorService`: Handles processing of raw WIP data
- `ExportService`: Generates Excel exports of dashboard data

### Database Queries

The dashboard uses optimized database queries to retrieve:
- Target capacity by line, date, and cutoff
- Endtime forecasted lots by date, cutoff, worktype, and lottype
- Submitted lots by date, cutoff, worktype, and lottype
- Work-in-progress data for remaining calculations

## JavaScript Integration

The dashboard uses several JavaScript files:

1. `livewire-endtime.js`: Handles Livewire event listeners and updates
2. `endtime-buttons.js`: Manages button functionality and auto-refresh
3. `endtime-entry.js`: Handles the Add Endtime and Add Submitted modals
4. `custom.js`: Provides general UI functionality and loading indicators

Key JavaScript functions:
- `showToast()`: Displays toast notifications using SweetAlert2
- `updateProgressChartFromCards()`: Updates the progress chart based on card values
- `setupAutoRefreshIntervals()`: Sets up intervals for auto-refresh
- `showLoadingOverlay()`: Shows the loading overlay during page reloads
- `lookupLot()`: Performs AJAX requests to look up lot details

## Loading and Performance

### Loading Indicators

The dashboard uses several types of loading indicators:
1. **Full-screen Overlay**: Shows during page reloads with the loader.svg animation
2. **Component Spinners**: Each component shows a spinner during data loading
3. **Button Spinners**: Buttons show spinners when actions are in progress

### Performance Considerations

To maintain optimal performance:
1. **Session Storage**: Filter values are stored in the session to minimize database lookups
2. **Selective Refreshing**: Only necessary components are refreshed when filters change
3. **Page Reloads**: Full page reloads are used instead of Livewire refreshes for better performance
4. **Optimized Queries**: Database queries are optimized with proper indexing
5. **Caching**: Frequently accessed data is cached to reduce database load

## Error Handling

The dashboard implements comprehensive error handling:
1. **Try-Catch Blocks**: All database operations are wrapped in try-catch blocks
2. **Logging**: Errors are logged for debugging and monitoring
3. **User Notifications**: Friendly error messages are shown to users
4. **Fallback Values**: Default values are used when data cannot be retrieved

## Key Files

1. `app/Livewire/EndtimeDashboard.php` - Main Livewire component
2. `resources/views/livewire/endtime-dashboard.blade.php` - Main template
3. `app/Livewire/EndtimeDashboard/*.php` - Child components
4. `app/Services/EndtimeDashboardService.php` - Business logic service
5. `public/js/livewire-endtime.js` - JavaScript integration
6. `public/js/endtime-buttons.js` - Button functionality
7. `public/js/endtime-entry.js` - Modal functionality
8. `public/images/banners/loader.svg` - Loading animation

## Recent Improvements

The dashboard has been recently improved to address performance issues:

1. **Page Reload Mechanism**: Changed from Livewire refresh to full page reload for filter changes
2. **Loading Overlay**: Added a full-screen loading overlay during page reloads
3. **Selective Auto-Refresh**: Modified auto-refresh to only trigger when turned ON
4. **Event Listener Optimization**: Added JavaScript event listeners to show loading indicators
5. **CSS Improvements**: Optimized CSS for better loading indicator display

These improvements have significantly reduced the dashboard refresh time from 20+ seconds to 3-5 seconds, providing a much better user experience.

<?php

return [
    'title_list'            => 'Hotel Asset Management',
    'subtitle_list'         => 'Facilities inventory, damage history tracking, and QR Code label printing.',
    'title_create'          => 'Add New Hotel Asset',
    'subtitle_create'       => 'The system automatically generates a QR Code file for this new asset.',
    'title_edit'            => 'Edit Asset: {0}',
    'title_detail'          => 'Asset Details: {0}',
    'title_print_qr'        => 'Print Asset QR Code Labels',
    'btn_print_all_qr'      => 'Print All QR Codes',
    'btn_add_asset'         => 'Add New Asset',
    
    // Asset Conditions
    'cond_good'             => 'Good',
    'cond_light_damage'     => 'Light Damage',
    'cond_heavy_damage'     => 'Heavy Damage',
    
    // Table Columns
    'col_qr'                => 'QR Code',
    'col_code'              => 'Asset Code',
    'col_name'              => 'Asset Name',
    'col_dept'              => 'Department',
    'col_location'          => 'Specific Location',
    'col_condition'         => 'Condition',
    'col_actions'           => 'Actions',
    
    // Form fields
    'field_code'            => 'Unique Asset Code',
    'field_code_help'       => 'Use standardized departmental code format.',
    'field_code_locked'     => 'Asset code cannot be modified once QR Code is generated.',
    'field_name'            => 'Asset Name / Equipment',
    'field_dept'            => 'Responsible Department',
    'field_select_dept'     => '-- Select Department --',
    'field_condition'       => 'Current Asset Condition',
    'field_location'        => 'Specific Placement Location',
    'btn_save_asset'        => 'Save & Generate QR Code',
    'btn_save_changes'      => 'Save Changes',
    
    // Detail Page
    'report_damage_this'    => 'Report Damage for This Asset',
    'print_qr_btn'          => 'Print QR',
    'qr_label_title'        => 'Asset QR Code Label',
    'download_qr_image'     => 'Download QR Image File',
    'facility_specs'        => 'Facility Specifications',
    'physical_condition'    => 'Physical Condition:',
    'physical_location'     => 'Physical Location:',
    'registration_date'     => 'Registration Date:',
    'total_incidents'       => 'Total Incidents:',
    'incident_count'        => '{0} Repairs Recorded',
    'repair_history_title'  => 'Repair & Maintenance History',
    'repair_history_sub'    => 'All Work Order tickets on record that serviced this asset.',
    'report_new_damage'     => 'Report New Damage',
    'prime_condition'       => 'Asset Condition Is Prime',
    'no_damage_recorded'    => 'No repair history or damage reports on record for this asset.',
    
    // Scan Page
    'scan_title'            => 'Asset QR Code Scanner',
    'scan_subtitle'         => 'Point your camera at the physical QR code label or select from quick simulation below.',
    'open_camera'           => 'Start Camera',
    'close_camera'          => 'Stop Camera',
    'quick_simulation'      => 'Quick Scan Simulation (For Testing & Demo):',
    'asset_detected_title'  => 'Asset Successfully Detected!',
    'managing_dept'         => 'Managing Department:',
    'current_condition'     => 'Current Asset Condition:',
    'btn_report_locked'     => 'Report Damage for This Asset (Auto-Locks WO Form)',
    'btn_view_history'      => 'View Condition & Repair History',
    'print_sheet_title'     => 'Golden Hill Hotel Batu Asset QR Code Printable Labels',
    'print_sheet_ready'     => 'Total {0} Assets Ready to Print',
    'print_sheet_btn'       => 'Print Labels Now',
    
    // Camera & Fallback Mitigation
    'torch_toggle'          => 'Flashlight / Torch',
    'switch_camera'         => 'Switch Camera',
    'camera_error_denied'   => 'Camera access was denied or not detected. Please enable camera permission in your browser or use the fallback options below.',
    'camera_error_dark'     => 'Dim lighting? Toggle flashlight or enter the asset code directly.',
    'fallback_manual_title' => 'Camera Scanner Fallback / Alternative Workflow',
    'fallback_manual_input' => 'Manual Asset Code Input',
    'fallback_dropdown_select' => 'Select Asset from Hotel Directory',
    'fallback_select_placeholder' => '-- Select Asset from List (Quick Search) --',
    'btn_process_code'      => 'Search & Process Asset',
];

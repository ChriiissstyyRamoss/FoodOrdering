<?php
// Expects $r['status']. Prints a colored Bootstrap badge.
$badgeColors = [
    'Pending' => 'secondary',
    'Preparing' => 'warning text-dark',
    'Out for Delivery' => 'info text-dark',
    'Delivered' => 'success',
    'Cancelled' => 'danger',
];
$c = $badgeColors[$r['status']] ?? 'secondary';
echo '<span class="badge badge-status bg-' . $c . '">' . e($r['status']) . '</span>';

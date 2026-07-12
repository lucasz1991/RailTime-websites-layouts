<?php
require_once __DIR__ . '/segments.php';

function rt_home(int $theme): void {
    $names = [1 => 'Noir Motion', 2 => 'Signal Compact', 3 => 'Industrial Grid', 4 => 'Atlas Editorial', 5 => 'Horizon Signature'];
    $rt = rt_document_start($names[$theme] ?? 'Layout ' . $theme, $theme, true); ?>
<main><?php
    rt_segment_hero($rt, $theme);
    rt_segment_metrics($rt);
    rt_segment_intro($rt, $theme);
    rt_segment_services($rt, $theme);
    rt_segment_process($rt);
    rt_segment_team($rt);
    rt_segment_map_technology($rt, $theme);
    rt_segment_emergency($rt); ?>
</main><?php rt_footer($rt); rt_document_end(true);
}

<?php
function get_status_info(string $status): array {
    $status = strtolower(trim($status));
    switch ($status) {
        case 'pending':
            return [
                'label'      => 'Diajukan',
                'class'      => 'bg-label-danger',
                'bg_class'   => 'bg-danger',
                'text_class' => 'text-danger',
                'icon'       => 'bx bx-time'
            ];
        case 'on progress':
            return [
                'label'      => 'Diproses',
                'class'      => 'bg-label-warning',
                'bg_class'   => 'bg-warning',
                'text_class' => 'text-warning',
                'icon'       => 'bx bx-loader-circle'
            ];
        case 'resolve':
            return [
                'label'      => 'Selesai',
                'class'      => 'bg-label-success',
                'bg_class'   => 'bg-success',
                'text_class' => 'text-success',
                'icon'       => 'bx bx-check-circle'
            ];
        case 'dibatalkan':
            return [
                'label'      => 'Dibatalkan',
                'class'      => 'bg-label-secondary',
                'bg_class'   => 'bg-secondary',
                'text_class' => 'text-secondary',
                'icon'       => 'bx bx-x-circle'
            ];
        case 'ditolak':
            return [
                'label'      => 'Ditolak',
                'class'      => 'bg-label-danger',
                'bg_class'   => 'bg-danger',
                'text_class' => 'text-danger',
                'icon'       => 'bx bx-block'
            ];
        default:
            return [
                'label'      => ucfirst($status),
                'class'      => 'bg-label-secondary',
                'bg_class'   => 'bg-secondary',
                'text_class' => 'text-secondary',
                'icon'       => 'bx bx-help-circle'
            ];
    }
}

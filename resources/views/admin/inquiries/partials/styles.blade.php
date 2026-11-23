<style>
    .inq-stat-card {
        cursor: pointer;
        transition: all .2s ease;
        border: 1px solid #E5E7EB;
    }

    .inq-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
    }

    .inq-stat-card.active {
        border-color: var(--inq-color);
        box-shadow: 0 0 0 1px var(--inq-color);
    }

    .inq-stat-card .inq-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        color: #fff;
        font-size: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--inq-color);
    }

    .inq-c-primary {
        --inq-color: #F26522;
    }

    .inq-c-warning {
        --inq-color: #FFC107;
    }

    .inq-c-success {
        --inq-color: #03C95A;
    }

    .inq-c-danger {
        --inq-color: #E70D0D;
    }

    .inq-search-box {
        position: relative;
    }

    .inq-search-box>i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
    }

    .inq-search-box input {
        padding-left: 36px;
    }

    .inq-table-wrap {
        position: relative;
        min-height: 220px;
    }

    .inq-loader {
        position: absolute;
        inset: 0;
        z-index: 5;
        background: rgba(255, 255, 255, .7);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .inq-spinner {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 3px solid #F3F4F6;
        border-top-color: #F26522;
        animation: inq-spin .7s linear infinite;
    }

    @keyframes inq-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .inq-table th {
        white-space: nowrap;
    }

    .inq-table td {
        vertical-align: middle;
    }

    .inq-thumb {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #E5E7EB;
        flex-shrink: 0;
    }

    .inq-thumb-ph {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        background: #F3F4F6;
        color: #9CA3AF;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .inq-initial {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        flex-shrink: 0;
        font-weight: 600;
        background: #FFF0E8;
        color: #F26522;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .inq-sub {
        font-size: 12px;
        color: #6B7280;
    }

    .inq-msg {
        max-width: 170px;
        display: inline-block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .inq-view-btn {
        border: 1px solid #FBD5C0;
        background: #FFF0E8;
        color: #F26522;
        font-size: 12px;
        font-weight: 600;
        border-radius: 6px;
        padding: 3px 10px;
        white-space: nowrap;
    }

    .inq-view-btn:hover {
        background: #F26522;
        color: #fff;
        border-color: #F26522;
    }

    .inq-status-select {
        min-width: 125px;
        font-size: 12.5px;
        font-weight: 600;
        border-radius: 20px;
        padding-top: 4px;
        padding-bottom: 4px;
        cursor: pointer;
    }

    .inq-status-select.status-warning {
        background-color: #FFF8E1;
        color: #B78103;
        border-color: #FFE08A;
    }

    .inq-status-select.status-success {
        background-color: #E6F9EE;
        color: #028A3D;
        border-color: #A6EBC4;
    }

    .inq-status-select.status-danger {
        background-color: #FDE7E7;
        color: #C20B0B;
        border-color: #F7B4B4;
    }

    .inq-empty {
        padding: 40px 0;
        text-align: center;
        color: #6B7280;
    }

    .inq-empty i {
        font-size: 38px;
        color: #D1D5DB;
        display: block;
        margin-bottom: 6px;
    }

    .inq-modal-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px 20px;
    }

    .inq-modal-meta .label {
        font-size: 12px;
        color: #6B7280;
        margin-bottom: 2px;
    }

    .inq-modal-meta .value {
        font-size: 14px;
        font-weight: 500;
        color: #111827;
        word-break: break-word;
    }

    .inq-modal-msg {
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 14px 16px;
        font-size: 14px;
        line-height: 1.7;
        white-space: pre-wrap;
        word-break: break-word;
        max-height: 50vh;
        overflow-y: auto;
    }

    @media (max-width: 575px) {
        .inq-modal-meta {
            grid-template-columns: 1fr;
        }
    }

    #inqPagination .pagination {
        margin-bottom: 0;
    }
</style>
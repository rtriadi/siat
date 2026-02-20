<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="report-footer-container">
    <table class="signature-table">
        <tr>
            <td class="sig-cell">
                <?php $ppk = null; foreach($signatories as $s) if($s['role_code'] == 'ppk') $ppk = $s; ?>
                <?php if($ppk): ?>
                    <div class="sig-role">Mengetahui,</div>
                    <div class="sig-role-name"><?= htmlspecialchars($ppk['role_name']) ?></div>
                    <div class="sig-space"></div>
                    <div class="sig-user-name"><?= htmlspecialchars($ppk['user_name'] ?? '....................................') ?></div>
                    <div class="sig-nip">NIP. <?= htmlspecialchars($ppk['user_nip'] ?? '....................................') ?></div>
                <?php endif; ?>
            </td>
            <td width="30%"></td>
            <td class="sig-cell">
                <div class="sig-date"><?= date('d', strtotime($print_date)) . ' ' . ucwords(strtolower(bulanIndo(date('m', strtotime($print_date))))) . ' ' . date('Y', strtotime($print_date)) ?></div>
                <?php $bendahara = null; foreach($signatories as $s) if($s['role_code'] == 'bendahara') $bendahara = $s; ?>
                <?php if($bendahara): ?>
                    <div class="sig-role-name"><?= htmlspecialchars($bendahara['role_name']) ?></div>
                    <div class="sig-space"></div>
                    <div class="sig-user-name"><?= htmlspecialchars($bendahara['user_name'] ?? '....................................') ?></div>
                    <div class="sig-nip">NIP. <?= htmlspecialchars($bendahara['user_nip'] ?? '....................................') ?></div>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="height: 40px;"></td>
        </tr>
        <tr>
            <td colspan="3" class="sig-center">
                <?php $pengelola = null; foreach($signatories as $s) if($s['role_code'] == 'pengelola') $pengelola = $s; ?>
                <?php if($pengelola): ?>
                    <div class="sig-role-name"><?= htmlspecialchars($pengelola['role_name']) ?></div>
                    <div class="sig-space"></div>
                    <div class="sig-user-name"><?= htmlspecialchars($pengelola['user_name'] ?? '....................................') ?></div>
                    <div class="sig-nip">NIP. <?= htmlspecialchars($pengelola['user_nip'] ?? '....................................') ?></div>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

<style>
    .report-footer-container {
        margin-top: 40px;
        page-break-inside: avoid;
    }
    .signature-table {
        width: 100%;
        border: none !important;
    }
    .signature-table td {
        border: none !important;
        padding: 0 !important;
        text-align: center;
        vertical-align: top;
    }
    .sig-cell {
        width: 35%;
    }
    .sig-center {
        text-align: center;
    }
    .sig-role, .sig-date {
        margin-bottom: 2px;
        font-size: 11px;
    }
    .sig-role-name {
        font-weight: bold;
        font-size: 11px;
        margin-bottom: 10px;
    }
    .sig-space {
        height: 60px;
    }
    .sig-user-name {
        font-weight: bold;
        text-decoration: underline;
        font-size: 11px;
    }
    .sig-nip {
        font-size: 11px;
    }
</style>

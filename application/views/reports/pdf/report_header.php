<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="report-header-container">
    <table class="kop-table">
        <tr>
            <td width="90" class="kop-logo-cell">
                <img src="<?= base_url('assets/dist/img/logopakota.png') ?>" alt="Logo" class="kop-logo">
            </td>
            <td class="kop-text-cell">
                <div class="kop-line-1">MAHKAMAH AGUNG REPUBLIK INDONESIA</div>
                <div class="kop-line-2">DIREKTORAT JENDERAL BADAN PERADILAN AGAMA</div>
                <div class="kop-line-3">PENGADILAN TINGGI AGAMA GORONTALO</div>
                <div class="kop-line-4">PENGADILAN AGAMA GORONTALO</div>
                <div class="kop-address">
                    Jalan Achmad Nadjamudin No.22, Dulalowo Timur, Kecamatan Kota Tengah<br>
                    Kota Gorontalo, 96138. <u>www.pa-gorontalo.go.id</u>, <u>surat@pa-gorontalo.go.id</u>
                </div>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>
</div>

<style>
    .report-header-container {
        margin-bottom: 20px;
        text-align: center;
        font-family: "Bookman Old Style", "Times New Roman", Times, serif;
    }
    .kop-table {
        width: 100%;
        border: none !important;
    }
    .kop-table td {
        border: none !important;
        padding: 0 !important;
        vertical-align: middle;
    }
    .kop-logo-cell {
        text-align: left;
    }
    .kop-logo {
        width: 85px;
        height: auto;
    }
    .kop-text-cell {
        text-align: center;
        padding-right: 90px !important; /* Offset for logo center alignment */
    }
    .kop-line-1, .kop-line-2, .kop-line-3, .kop-line-4 {
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 2px;
        font-size: 13pt;
        letter-spacing: 0px;
    }
    .kop-line-4 {
        margin-bottom: 6px;
    }
    
    .kop-address {
        font-size: 8pt;
        font-weight: normal;
        color: #000;
        line-height: 1.3;
    }
    .kop-line {
        border-bottom: 3px solid #000;
        margin-top: 10px;
        position: relative;
    }
    .kop-line::after {
        content: "";
        display: block;
        border-bottom: 1px solid #000;
        margin-top: 2px;
    }
</style>

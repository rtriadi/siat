<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - SIAT</title>
    <style>
        /* Screen styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f0f0f0;
        }

        <?php
        $p_width = '210mm';
        $p_height = '297mm';
        $print_size_css = 'A4';

        if (isset($paper_size) && $paper_size === 'F4') {
            $p_width = '215.9mm';
            $p_height = '330.2mm';
            $print_size_css = '215.9mm 330.2mm';
        }

        $page_width = isset($print_orientation) && $print_orientation === 'landscape' ? $p_height : $p_width;
        $page_height = isset($print_orientation) && $print_orientation === 'landscape' ? $p_width : $p_height;
        ?>

        .print-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: #1e293b;
            color: white;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .print-toolbar span {
            flex: 1;
            font-size: 15px;
            font-weight: 600;
        }

        .toolbar-btn {
            padding: 8px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .btn-print {
            background: #3b82f6;
            color: white;
        }

        .btn-print:hover {
            background: #2563eb;
        }

        .btn-close {
            background: #4b5563;
            color: white;
        }

        .btn-close:hover {
            background: #374151;
        }

        .report-container {
            margin-top: 56px;
            padding: 24px;
            display: flex;
            justify-content: center;
        }

        .report-page {
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            width: <?= $page_width ?>;
            min-height: <?= $page_height ?>;
            padding: 15mm 15mm;
            box-sizing: border-box;
        }

        /* Print styles */
        @media print {
            @page {
                size: <?= $print_size_css ?> <?= isset($print_orientation) ? $print_orientation : 'portrait' ?>;
                margin: 10mm 15mm 15mm 15mm; /* increased bottom margin for footer */
                
                @bottom-right {
                    content: "Halaman " counter(page) " dari " counter(pages);
                    font-family: Arial, sans-serif;
                    font-size: 9px;
                    color: #555;
                }
                
                @bottom-left {
                    content: "SIAT (Sistem Inventori ATK Terpadu) - Pengadilan Agama Gorontalo";
                    font-family: Arial, sans-serif;
                    font-size: 9px;
                    color: #555;
                }
            }

            .print-toolbar {
                display: none !important;
            }

            body {
                background: white;
                margin: 0;
                padding: 0;
            }

            .report-container {
                margin: 0;
                padding: 0;
            }

            .report-page {
                width: 100%;
                min-height: auto;
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="print-toolbar">
        <span>&#128438; Cetak Laporan &mdash; SIAT</span>
        <button class="toolbar-btn btn-close" onclick="window.close()">
            &#10005; Tutup
        </button>
        <button class="toolbar-btn btn-print" onclick="window.print()">
            &#128438; Cetak / Simpan PDF
        </button>
    </div>

    <div class="report-container">
        <div class="report-page">
            <?= $report_html ?>
        </div>
    </div>

    <script>
        // Auto-trigger print dialog on load
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>

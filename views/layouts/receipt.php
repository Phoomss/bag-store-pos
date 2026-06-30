<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Bag Store POS</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #fff;
            color: #000;
            margin: 0;
            padding: 10px;
            font-size: 12px;
            width: 80mm; /* standard thermal roll width */
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .header {
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        .details p {
            margin: 3px 0;
            font-size: 11px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            font-size: 11px;
            padding: 3px 0;
            text-align: left;
        }
        .totals-table {
            width: 100%;
            margin-top: 5px;
        }
        .totals-table td {
            font-size: 11px;
            padding: 3px 0;
        }
        @media print {
            body {
                width: 100%;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        .btn-print {
            background-color: #2563eb;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-family: sans-serif;
            font-size: 12px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-top: 10px;">
        <button class="btn-print" onclick="window.print()">Print Receipt</button>
        <button class="btn-print" onclick="window.close()" style="background-color: #4b5563;">Close Window</button>
    </div>

    <?= $viewContent ?>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Auto trigger print
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Backup Notification</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f7f5f0;
            color: #332d27;
            margin: 0;
            padding: 24px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e6ded3;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(117, 95, 62, 0.08);
        }
        .header {
            background: linear-gradient(135deg, #755f3e, #54422b);
            color: #ffffff;
            padding: 28px 32px;
            text-align: left;
        }
        .header h1 {
            margin: 0 0 6px 0;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: -0.3px;
        }
        .header p {
            margin: 0;
            font-size: 13px;
            color: #e4d7c5;
        }
        .body {
            padding: 32px;
        }
        .badge-success {
            display: inline-block;
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .table-info {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table-info tr {
            border-bottom: 1px solid #f1ece4;
        }
        .table-info tr:last-child {
            border-bottom: none;
        }
        .table-info td {
            padding: 10px 4px;
            font-size: 14px;
        }
        .table-info td.label {
            color: #7d7266;
            width: 40%;
            font-weight: 500;
        }
        .table-info td.value {
            color: #24201c;
            font-weight: 600;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .alert-box {
            background: #fbf9f6;
            border-left: 4px solid #755f3e;
            padding: 14px 18px;
            border-radius: 4px;
            font-size: 13px;
            color: #5d5246;
            margin-top: 24px;
            line-height: 1.5;
        }
        .footer {
            padding: 20px 32px;
            background: #faf8f5;
            border-top: 1px solid #f1ece4;
            text-align: center;
            font-size: 12px;
            color: #8c8175;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Database Backup Selesai</h1>
            <p>{{ $appName ?? 'Project Support' }} Automated Backup System</p>
        </div>
        <div class="body">
            <span class="badge-success">Backup Berhasil Dibuat</span>

            <p style="margin-top: 0; font-size: 14px; line-height: 1.6; color: #473e35;">
                Halo Pak Veni, file backup database MySQL untuk aplikasi <strong>{{ $appName ?? 'Project Support' }}</strong> telah berhasil digenerate dan dilampirkan pada email ini.
            </p>

            <table class="table-info">
                <tr>
                    <td class="label">Nama Database</td>
                    <td class="value">{{ $meta['database'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Database Host</td>
                    <td class="value">{{ $meta['host'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Nama File Backup</td>
                    <td class="value">{{ $meta['filename'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Ukuran File</td>
                    <td class="value">{{ $meta['size_formatted'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jumlah Tabel</td>
                    <td class="value">{{ $meta['tables_count'] ?? 0 }} Tabel</td>
                </tr>
                <tr>
                    <td class="label">Waktu Backup</td>
                    <td class="value">{{ $meta['created_at'] ?? now('Asia/Jakarta')->toDateTimeString() }}</td>
                </tr>
                <tr>
                    <td class="label">Durasi Eksekusi</td>
                    <td class="value">{{ $meta['duration'] ?? '0' }} detik</td>
                </tr>
            </table>

            <div class="alert-box">
                <strong>Catatan Keamanan:</strong><br>
                File backup terkompresi <code>.sql.gz</code> berisi seluruh skema dan data tabel MySQL. Simpan file ini di tempat penyimpanan yang aman.
            </div>
        </div>
        <div class="footer">
            Email ini dikirim secara otomatis oleh sistem {{ $appName ?? 'Project Support' }}.
        </div>
    </div>
</body>
</html>

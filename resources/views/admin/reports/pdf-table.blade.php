<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $filename }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; font-weight: 600; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>{{ ucfirst(str_replace('-', ' ', $filename)) }}</h1>
    <p>Generated: {{ now()->format('M d, Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                @foreach($headers as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach($headers as $h)
                        <td>{{ $row[$h] ?? '-' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

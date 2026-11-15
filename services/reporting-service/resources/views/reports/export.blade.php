<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }
        .header { border-bottom: 3px solid #1f4e79; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { font-size: 18px; color: #1f4e79; margin: 0 0 4px 0; }
        .header .meta { color: #555; font-size: 10px; }
        .warnings { background: #fff3cd; border: 1px solid #ffe69c; padding: 8px 10px; margin-bottom: 12px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1f4e79; color: #fff; text-align: left; padding: 6px 8px; font-size: 10px; }
        td { border-bottom: 1px solid #ddd; padding: 5px 8px; }
        tr:nth-child(even) td { background: #f4f7fa; }
        .empty { text-align: center; color: #777; padding: 20px; }
        .footer { margin-top: 15px; font-size: 9px; color: #888; border-top: 1px solid #ccc; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="meta">
            Généré le {{ $generatedAt }} — Par {{ $userName }} — Plateforme PATRIA
        </div>
    </div>

    @if (!empty($warnings))
        <div class="warnings">
            <strong>Données incomplètes :</strong>
            <ul style="margin: 4px 0 0 16px; padding: 0;">
                @foreach ($warnings as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                @foreach ($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ max(count($headings), 1) }}" class="empty">Aucune donnée disponible.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Document généré automatiquement par le service reporting — {{ config('app.name') }}
    </div>
</body>
</html>

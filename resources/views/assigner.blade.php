<!DOCTYPE html>
<html>
    <head>
        <title>3人以上にレビューされている割合</title>
        <style>
        body {
            color: #666;
            padding: 1em;
        }
        * {
            font-size: 100%;
        }
        table, th, td {
            border-collapse: collapse;
            border: 1px solid #ddd;
            padding: 0.5em;
        }
        table {
            width: 100%;
        }
        th {
            background-color: #eee;
        }
        td {
            text-align: right;
        }
        </style>
    </head>
    <body>
        <h1>3人以上にレビューされている割合</h1>
        <table>
            <thead>
                <tr>
                    <th>レーン</th>
                    <th>カード数</th>
                    <th>3人以上のレビューア</th>
                    <th>レビューにかかった平均日数</th>
                    <th>割合</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assigners as $data)
                    <tr>
                        <td>{{ $data['listName'] }}</rd>
                        <td>{{ $data['allCardsCount'] }}</td>
                        <td>{{ $data['enoguhAssignerCount'] }}</td>
                        <td>{{ $data['dayAverage'] }}</td>
                        <td>{{ $data['enoguhAssignerRetio'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>

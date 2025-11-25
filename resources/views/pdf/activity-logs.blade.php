<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Activity Log Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #111;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #444;
            border-bottom: 2px solid #ddd;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-login { background-color: #dcfce7; color: #166534; }
        .badge-logout { background-color: #f4f4f5; color: #3f3f46; }
        .badge-created { background-color: #dbeafe; color: #1e40af; }
        .badge-updated { background-color: #fef3c7; color: #92400e; }
        .badge-deleted { background-color: #fee2e2; color: #991b1b; }
        .badge-default { background-color: #e0e7ff; color: #3730a3; }
        
        .details {
            font-size: 10px;
            color: #555;
        }
        .text-red { color: #dc2626; }
        .text-green { color: #16a34a; }
        .text-blue { color: #2563eb; }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 10px;
            text-align: center;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ \App\Models\Setting::getValue('business_name', 'POS System') }} - Activity Logs</h1>
        <p>Generated on {{ now()->format('d M Y H:i') }} | By {{ auth()->user()->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%">Time</th>
                <th style="width: 15%">User</th>
                <th style="width: 10%">Action</th>
                <th style="width: 25%">Description</th>
                <th style="width: 35%">Details</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>
                        {{ $log->created_at->format('d M Y') }}<br>
                        <span style="color: #666;">{{ $log->created_at->format('H:i:s') }}</span>
                    </td>
                    <td>
                        <strong>{{ $log->user?->name ?? 'System' }}</strong><br>
                        <span style="color: #666; font-size: 10px;">{{ $log->user?->email }}</span>
                    </td>
                    <td>
                        @php
                            $badgeClass = match($log->action) {
                                'login' => 'badge-login',
                                'logout' => 'badge-logout',
                                'created' => 'badge-created',
                                'updated' => 'badge-updated',
                                'deleted' => 'badge-deleted',
                                default => 'badge-default'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td>{{ $log->description }}</td>
                    <td class="details">
                        @if($log->properties)
                            @if(isset($log->properties['old_quantity']) && isset($log->properties['new_quantity']))
                                <div>
                                    Quantity: 
                                    <span class="text-red">{{ $log->properties['old_quantity'] }}</span> 
                                    → 
                                    <span class="text-green">{{ $log->properties['new_quantity'] }}</span>
                                </div>
                                @if(isset($log->properties['product']))
                                    <div>Product: {{ $log->properties['product'] }}</div>
                                @endif
                                @if(isset($log->properties['branch']))
                                    <div>Branch: {{ $log->properties['branch'] }}</div>
                                @endif
                            @endif

                            @if(isset($log->properties['changes']))
                                @foreach($log->properties['changes'] as $field => $change)
                                    <div>
                                        {{ ucfirst(str_replace('_', ' ', $field)) }}:
                                        @if($field === 'price' || $field === 'cost')
                                            <span class="text-red">{{ number_format($change['old'] ?? 0, 0, ',', '.') }}</span>
                                            →
                                            <span class="text-green">{{ number_format($change['new'] ?? 0, 0, ',', '.') }}</span>
                                        @elseif($field === 'is_active')
                                            <span class="text-red">{{ $change['old'] ? 'Active' : 'Inactive' }}</span>
                                            →
                                            <span class="text-green">{{ $change['new'] ? 'Active' : 'Inactive' }}</span>
                                        @else
                                            <span class="text-red">{{ is_bool($change['old'] ?? null) ? ($change['old'] ? 'Yes' : 'No') : ($change['old'] ?? '-') }}</span>
                                            →
                                            <span class="text-green">{{ is_bool($change['new'] ?? null) ? ($change['new'] ? 'Yes' : 'No') : ($change['new'] ?? '-') }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            @endif

                            @if(isset($log->properties['added']) && !empty($log->properties['added']))
                                <div class="text-green">
                                    + {{ implode(', ', $log->properties['added']) }}
                                </div>
                            @endif

                            @if(isset($log->properties['removed']) && !empty($log->properties['removed']))
                                <div class="text-red">
                                    - {{ implode(', ', $log->properties['removed']) }}
                                </div>
                            @endif

                            @if(isset($log->properties['menus']) && !empty($log->properties['menus']))
                                <div class="text-blue">
                                    {{ implode(', ', $log->properties['menus']) }}
                                </div>
                            @endif

                            @if(isset($log->properties['items']) && !empty($log->properties['items']))
                                <div><strong>Items:</strong></div>
                                @foreach($log->properties['items'] as $item)
                                    <div>• {{ $item }}</div>
                                @endforeach
                                <div style="margin-top: 2px;">
                                    <strong>Total:</strong> Rp {{ number_format($log->properties['total'] ?? 0, 0, ',', '.') }}
                                </div>
                                @if(isset($log->properties['branch']))
                                    <div class="text-blue">Branch: {{ $log->properties['branch'] }}</div>
                                @endif
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Page <span class="page-number"></span>
    </div>
</body>
</html>

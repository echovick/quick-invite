<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Event Pass - {{ $event->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            padding: 40px;
            background: #ffffff;
        }

        .pass-container {
            max-width: 600px;
            margin: 0 auto;
            border: 3px solid #2563eb;
            border-radius: 12px;
            padding: 40px;
            background: #ffffff;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }

        .event-title {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .event-subtitle {
            font-size: 14px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .main-content {
            width: 100%;
            margin: 30px 0;
        }

        .left-section {
            width: 48%;
            float: left;
            margin-right: 4%;
        }

        .right-section {
            width: 48%;
            float: left;
            text-align: center;
        }

        .clearfix {
            clear: both;
        }

        .attendee-info {
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            color: #1f2937;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .table-number {
            text-align: center;
            padding: 30px 20px;
            background: #dbeafe;
            border: 2px solid #93c5fd;
            border-radius: 8px;
            color: #1e40af;
        }

        .table-label {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #3b82f6;
        }

        .table-value {
            font-size: 48px;
            font-weight: bold;
            line-height: 1;
            color: #1e40af;
        }

        .plus-one {
            margin-top: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #3b82f6;
        }

        .qr-code {
            margin-top: 0;
        }

        .qr-code img {
            width: 180px;
            height: 180px;
            border: 3px solid #e5e7eb;
            padding: 10px;
            background: white;
        }

        .qr-label {
            font-size: 11px;
            color: #6b7280;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .event-details {
            margin: 30px 0;
            padding: 20px;
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 4px;
        }

        .detail-row {
            margin-bottom: 12px;
            line-height: 1.6;
        }

        .detail-label {
            font-weight: 600;
            color: #92400e;
            display: inline-block;
            min-width: 100px;
        }

        .detail-value {
            color: #78350f;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
        }

        .footer-text {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="pass-container">
        <div class="header">
            <div class="event-title">{{ $event->title }}</div>
            <div class="event-subtitle">Official Event Pass</div>
        </div>

        <div class="main-content">
            <div class="left-section">
                <div class="attendee-info">
                    <div class="info-label">Attendee Name</div>
                    <div class="info-value">{{ $invite->is_reserved ? 'Reserved' : $invite->invitee_name }}</div>

                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{{ $invite->is_reserved ? 'Reserved' : $invite->invitee_phone }}</div>
                </div>

                <div class="table-number">
                    <div class="table-label">TABLE NUMBER</div>
                    <div class="table-value">{{ $invite->table_number }}</div>
                    @if($invite->has_plus_one)
                        <div class="plus-one">+ 1 Guest</div>
                    @endif
                </div>
            </div>

            <div class="right-section">
                <div class="qr-code">
                    <img src="{{ $qrCode }}" alt="QR Code">
                    <div class="qr-label">Scan to Verify</div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="event-details">
            <div class="detail-row">
                <span class="detail-label">Location:</span>
                <span class="detail-value">{{ $event->address }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date & Time:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($event->event_time)->format('F j, Y \a\t g:i A') }}</span>
            </div>
        </div>

        <div class="footer">
            <div class="footer-text">
                Please present this pass at the event entrance.<br>
                For any inquiries, please contact the event organizer.
            </div>
        </div>
    </div>
</body>
</html>

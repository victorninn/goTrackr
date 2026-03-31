<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; background: #f4f4f4; margin: 0; padding: 30px; }
        .card { background: #fff; border-radius: 10px; padding: 32px; max-width: 520px; margin: auto; border: 1px solid #e5e7eb; }
        .label { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px; }
        .value { font-size: 15px; color: #111827; font-weight: 600; margin-bottom: 18px; }
        .badge { display: inline-block; background: #017e83; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-bottom: 24px; }
        h2 { color: #011F4B; margin: 0 0 6px; }
        .footer { text-align: center; font-size: 12px; color: #9ca3af; margin-top: 24px; }
        hr { border: none; border-top: 1px solid #e5e7eb; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="card">
        <span class="badge">goTrackr Beta</span>
        <h2>New Demo Request</h2>
        <p style="color:#6b7280;font-size:14px;margin:0 0 24px;">Someone is interested in goTrackr. Details below.</p>
        <hr>
        <div class="label">Full Name</div>
        <div class="value">{{ $demoName }}</div>
        <div class="label">Business / Company</div>
        <div class="value">{{ $demoBusiness }}</div>
        <div class="label">Contact Number</div>
        <div class="value">{{ $demoPhone }}</div>
        <div class="label">Email Address</div>
        <div class="value"><a href="mailto:{{ $demoEmail }}" style="color:#017e83;">{{ $demoEmail }}</a></div>
        <hr>
        <div class="footer">Sent from goTrackr · {{ date('F d, Y') }}</div>
    </div>
</body>
</html>

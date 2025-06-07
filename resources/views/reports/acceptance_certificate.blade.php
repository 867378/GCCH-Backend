<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Acceptance Certificate</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            text-align: center;
            padding: 50px;
        }
        .certificate-container {
            border: 10px double #000;
            padding: 40px;
        }
        .header {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 40px;
        }
        .content {
            font-size: 18px;
            margin: 30px 0;
            line-height: 1.8;
        }
        .signature {
            margin-top: 30px;
            text-align: right;
            margin-right: 30px;
        }
        .signature2 {
            margin-bottom: 30px;
            text-align: left;
            margin-left: 30px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="header">Certificate of Job Acceptance</div>

        <div class="content">
            This is to certify that<br><br>
            <strong>{{ $application->applicant->first_name }} {{ $application->applicant->last_name }}</strong><br><br>
            has been officially accepted for the position of<br><br>
            <strong>{{ $application->job->job_title }}</strong><br><br>
            at <strong>{{ $application->job->company->company_name ?? 'the hiring company' }}</strong>.<br><br>
            <small>Date Accepted: {{ \Carbon\Carbon::parse($application->updated_at)->format('F j, Y') }}</small>
        </div>

        <div class="signature">
            <strong>{{ $application->job->company->company_name ?? 'Hiring Company' }}</strong><br>
            Company Signature
        </div>

        <div class="signature2">
            <strong>{{ $application->applicant->first_name }} {{ $application->applicant->last_name }}</strong><br>
            Applicant Signature
        </div>
    </div>
</body>
</html>

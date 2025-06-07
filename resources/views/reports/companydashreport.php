<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Company Dashboard Report Certificate</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .certificate {
            border: 2px solid #045d56;
            padding: 40px;
            position: relative;
            background: #fff;
            box-shadow: 0 0 25px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 150px;
            margin-bottom: 20px;
        }

        .title {
            color: #045d56;
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
            text-transform: uppercase;
        }

        .subtitle {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 30px 0;
        }

        .metric {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #045d56;
        }

        .metric-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .metric-value {
            font-size: 24px;
            color: #045d56;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .signature {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature-line {
            width: 200px;
            border-top: 1px solid #333;
            margin-top: 10px;
            padding-top: 5px;
            text-align: center;
        }

        .date {
            margin-top: 20px;
            text-align: right;
            color: #666;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: -1;
            width: 400px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <img src="public/gcchnobg.png" alt="Watermark" class="watermark">
        
        <div class="header">
            <img src="<?php echo public_path('gcchnobg.png'); ?>" alt="GCCH Logo" class="logo">
            <div class="title">Company Dashboard Performance Report</div>
            <div class="subtitle">Official Analytics Certificate</div>
        </div>

        <p>This is to certify that</p>
        <h2><?php echo htmlspecialchars($companyName); ?></h2>
        <p>has achieved the following metrics for the period of <?php echo date('F Y'); ?></p>

        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Total Hired Applicants</div>
                <div class="metric-value"><?php echo $totalHired; ?></div>
            </div>

            <div class="metric">
                <div class="metric-label">Active Job Listings</div>
                <div class="metric-value"><?php echo $totalJobs; ?></div>
            </div>

            <div class="metric">
                <div class="metric-label">Pending Applications</div>
                <div class="metric-value"><?php echo $pendingApplications; ?></div>
            </div>

            <div class="metric">
                <div class="metric-label">Application Success Rate</div>
                <div class="metric-value"><?php echo $successRate; ?>%</div>
            </div>
        </div>

        <div class="footer">
            <p>This certificate validates the company's performance metrics as recorded in the Gordon College Career Hub system.</p>
            
            <div class="signature">
                <div class="signature-block">
                    <div class="signature-line">Company Representative</div>
                </div>
                <div class="signature-block">
                    <div class="signature-line">GCCH Administrator</div>
                </div>
            </div>

            <div class="date">
                Generated on: <?php echo date('F d, Y'); ?>
            </div>
        </div>
    </div>
</body>
</html>
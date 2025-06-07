<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Job Report - {{ $job->job_title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #000;
            margin: 30px;
        }
        h2 {
            text-align: left;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            border: 1px solid #bbb;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <!-- Job Report for: {{ $job->job_title }} -->
    <h1>Job Report: {{ $job->job_title }}</h1>
    <p><strong>Job Description:</strong> {{ $job->job_description }}</p>
    <p><strong>Location:</strong> {{ $job->job_location }}</p>
    <p><strong>Type:</strong> {{ $job->job_type }}</p>
    <p><strong>Monthly Salary:</strong> ₱{{ $job->monthly_salary }}</p>
    <p><strong>Date Posted:</strong> {{ $job->date_posted }}</p>
    <p><strong>Status:</strong> {{ $job->status }}</p>
    <p><strong>Slots Filled:</strong> {{ $job->filled_slots }} / {{ $job->total_slots }}</p>
    <p><strong>Recommended Courses:</strong> {{ $job->recommended_course }}, {{ $job->recommended_course_2 }}, {{ $job->recommended_course_3 }}</p>
    <p><strong>Recommended Expertise:</strong> {{ $job->recommended_expertise }}, {{ $job->recommended_expertise_2 }}, {{ $job->recommended_expertise_3 }}</p>

    <hr>

    <h2>Application Summary</h2>
    <ul>
        <li><strong>Total Applicants:</strong> {{ $job->applications->count() }}</li>
        <li><strong>Applied:</strong> {{ $job->applications->where('status', 'applied')->count() }}</li>
        <li><strong>Screening:</strong> {{ $job->applications->where('status', 'screening')->count() }}</li>
        <li><strong>For Interview:</strong> {{ $job->applications->where('status', 'for_interview')->count() }}</li>
        <li><strong>Interviewed:</strong> {{ $job->applications->where('status', 'interviewed')->count() }}</li>
        <li><strong>Accepted:</strong> {{ $job->applications->where('status', 'accepted')->count() }}</li>
        <li><strong>Rejected:</strong> {{ $job->applications->where('status', 'rejected')->count() }}</li>
        <li><strong>Hired:</strong> {{ $job->applications->where('status', 'hired')->count() }}</li>
    </ul>

    <hr>

    <h2>Applicants</h2>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Course</th>
                <th>Expertise</th>
                <th>Date Applied</th>
                <th>Status</th>
                <th>Interview Schedule</th>
                <th>Comment</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($job->applications as $idx => $app)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $app->applicant->first_name }} {{ $app->applicant->last_name }}</td>
                    <td>{{ $app->applicant->course }}</td>
                    <td>{{ $app->applicant->expertise }}</td>
                    <td>{{ $app->date_applied }}</td>
                    <td>{{ ucfirst($app->status) }}</td>
                    <td>{{ $app->scheduled_at ?? '-' }}</td>
                    <td>{{ $app->comment ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;">No applicants yet.</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    <h2>Hired Applicants</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Phone</th>
                <th>Date Hired</th>
                <th>Comment</th>
            </tr>
        </thead>
        <tbody>
            @php $hired = $job->applications->where('status', 'hired'); @endphp
            @forelse ($hired as $idx => $application)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $application->applicant->first_name }} {{ $application->applicant->last_name }}</td>
                    <td>{{ $application->applicant->user->email }}</td>
                    <td>{{ $application->applicant->course }}</td>
                    <td>{{ $application->applicant->phone_number }}</td>
                    <td>{{ $application->updated_at ? $application->updated_at->format('Y-m-d') : '-' }}</td>
                    <td>{{ $application->comment ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;">No hired applicants.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <hr>

    <p><em>Report generated on: {{ now()->format('Y-m-d H:i:s') }}</em></p>
</body>
</html>
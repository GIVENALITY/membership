@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Email Notifications</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h6 class="card-title">Welcome Email Template</h6>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label">Subject</label>
                  <input type="text" class="form-control" value="Welcome to Membership MS - Your Premium Dining Experience!" readonly>
                </div>
                <div class="mb-3">
                  <label class="form-label">Message Preview</label>
                  <textarea class="form-control" rows="6" readonly>Dear [Member Name],

Welcome to Membership MS! We're excited to have you as part of our premium dining community.

Your membership details:
- Membership ID: [MS001]
- Join Date: [Date]
- Current Discount: [5%]

Start earning rewards with every visit. The more you dine, the more you save!

Best regards,
The Membership MS Team</textarea>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="welcomeEmailEnabled" checked>
                  <label class="form-check-label" for="welcomeEmailEnabled">
                    Send welcome email automatically
                  </label>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h6 class="card-title">Birthday Email Template</h6>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label">Subject</label>
                  <input type="text" class="form-control" value="Happy Birthday! Special Discount Just for You!" readonly>
                </div>
                <div class="mb-3">
                  <label class="form-label">Message Preview</label>
                  <textarea class="form-control" rows="6" readonly>Dear [Member Name],

Happy Birthday! 🎉

We hope your special day is filled with joy and wonderful moments. As a valued member of our dining community, we'd like to offer you a special birthday discount.

Visit us this month and enjoy an extra 15% off your bill (in addition to your regular member discount)!

Your birthday gift awaits you at [Restaurant Name].

Best wishes,
The Membership MS Team</textarea>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="birthdayEmailEnabled" checked>
                  <label class="form-check-label" for="birthdayEmailEnabled">
                    Send birthday email automatically
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Birthday Alerts -->
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title">
          <i class="icon-base ri ri-cake-line text-warning me-2"></i>
          Upcoming Birthdays
        </h5>
        <button class="btn btn-warning btn-sm" onclick="sendBirthdayEmails()">
          <i class="icon-base ri ri-mail-send-line me-1"></i>
          Send Birthday Emails
        </button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Member</th>
                <th>Birthday</th>
                <th>Days Until</th>
                <th>Email Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div>
                      <h6 class="mb-0">John Doe</h6>
                      <small class="text-muted">john@example.com</small>
                    </div>
                  </div>
                </td>
                <td>December 15, 1990</td>
                <td><span class="badge bg-label-warning">3 days</span></td>
                <td><span class="badge bg-label-success">Sent</span></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" onclick="sendIndividualEmail('john@example.com')">
                    <i class="icon-base ri ri-mail-line"></i>
                    Resend
                  </button>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <img src="{{ asset('assets/img/avatars/2.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div>
                      <h6 class="mb-0">Jane Smith</h6>
                      <small class="text-muted">jane@example.com</small>
                    </div>
                  </div>
                </td>
                <td>December 20, 1985</td>
                <td><span class="badge bg-label-info">8 days</span></td>
                <td><span class="badge bg-label-secondary">Pending</span></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" onclick="sendIndividualEmail('jane@example.com')">
                    <i class="icon-base ri ri-mail-line"></i>
                    Send Now
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Email Log -->
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Email Log</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Date</th>
                <th>Recipient</th>
                <th>Type</th>
                <th>Subject</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Today, 10:30 AM</td>
                <td>john@example.com</td>
                <td><span class="badge bg-label-primary">Birthday</span></td>
                <td>Happy Birthday! Special Discount Just for You!</td>
                <td><span class="badge bg-label-success">Delivered</span></td>
              </tr>
              <tr>
                <td>Yesterday, 2:15 PM</td>
                <td>newmember@example.com</td>
                <td><span class="badge bg-label-info">Welcome</span></td>
                <td>Welcome to Membership MS - Your Premium Dining Experience!</td>
                <td><span class="badge bg-label-success">Delivered</span></td>
              </tr>
              <tr>
                <td>2 days ago, 9:45 AM</td>
                <td>jane@example.com</td>
                <td><span class="badge bg-label-primary">Birthday</span></td>
                <td>Happy Birthday! Special Discount Just for You!</td>
                <td><span class="badge bg-label-danger">Failed</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function sendBirthdayEmails() {
  if (confirm('Send birthday emails to all members with upcoming birthdays?')) {
    alert('Birthday emails sent successfully!');
  }
}

function sendIndividualEmail(email) {
  alert(`Birthday email sent to ${email}`);
}
</script>
@endsection 
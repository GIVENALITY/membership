# Event Management System

This document describes the new event management functionality added to the membership system.

## Overview

The event system allows hotels to create and manage events, with both internal member registration and public external registration capabilities.

## Features

### For Hotel Staff (Admin/Manager)
- Create, edit, and manage events
- Set event details (title, description, date/time, location, capacity, price)
- Upload event images
- Publish/draft/cancel events
- View and manage registrations
- Export registration data
- Register existing members for events
- Track registration status (pending, confirmed, cancelled, attended)

### For External Users
- Browse public events
- Register for events without being a member
- View event details and availability
- Cancel their own registrations
- Search for their registration by code

## Database Structure

### Events Table
- `id` - Primary key
- `hotel_id` - Foreign key to hotels table
- `title` - Event title
- `description` - Event description
- `image` - Event image path (optional)
- `start_date` - Event start date and time
- `end_date` - Event end date and time
- `location` - Event location (optional)
- `max_capacity` - Maximum number of attendees (null for unlimited)
- `price` - Price per person
- `is_public` - Whether external users can register
- `is_active` - Whether the event is active
- `status` - Event status (draft, published, cancelled, completed)
- `settings` - JSON field for additional settings

### Event Registrations Table
- `id` - Primary key
- `event_id` - Foreign key to events table
- `member_id` - Foreign key to members table (optional)
- `registration_code` - Unique registration code
- `name` - Registrant name
- `email` - Registrant email
- `phone` - Registrant phone (optional)
- `number_of_guests` - Number of guests
- `total_amount` - Total amount for registration
- `status` - Registration status (pending, confirmed, cancelled, attended)
- `special_requests` - Special requests (optional)
- `guest_details` - JSON field for additional guest information
- `registered_at` - Registration timestamp
- `confirmed_at` - Confirmation timestamp (optional)
- `cancelled_at` - Cancellation timestamp (optional)

## Routes

### Admin Routes (Protected)
- `GET /events` - List all events
- `GET /events/create` - Create event form
- `POST /events` - Store new event
- `GET /events/{event}` - View event details
- `GET /events/{event}/edit` - Edit event form
- `PUT /events/{event}` - Update event
- `DELETE /events/{event}` - Delete event
- `POST /events/{event}/publish` - Publish event
- `POST /events/{event}/cancel` - Cancel event
- `GET /events/{event}/registrations` - View registrations
- `POST /events/{event}/registrations/{registration}/confirm` - Confirm registration
- `POST /events/{event}/registrations/{registration}/cancel` - Cancel registration
- `POST /events/{event}/registrations/{registration}/attend` - Mark as attended
- `GET /events/{event}/export-registrations` - Export registrations

### Public Routes (Unprotected)
- `GET /events` - Browse all public events
- `GET /events/{hotelSlug}` - Browse events for specific hotel
- `GET /events/{hotelSlug}/{event}` - View public event details
- `GET /events/{hotelSlug}/{event}/register` - Registration form
- `POST /events/{hotelSlug}/{event}/register` - Process registration
- `GET /events/{hotelSlug}/{event}/registration/{registration}/confirmation` - Registration confirmation
- `POST /events/{hotelSlug}/{event}/registration/{registration}/cancel` - Cancel registration
- `GET /events/{hotelSlug}/search` - Search registration form
- `POST /events/{hotelSlug}/search` - Search for registration

## Usage

### Creating an Event
1. Navigate to Events in the admin panel
2. Click "Create Event"
3. Fill in event details:
   - Title and description
   - Start and end dates
   - Location (optional)
   - Maximum capacity (optional)
   - Price per person
   - Upload image (optional)
   - Set public/private status
4. Save as draft or publish immediately

### Managing Registrations
1. View event details
2. See registration statistics
3. View individual registrations
4. Confirm, cancel, or mark registrations as attended
5. Export registration data

### Public Registration
1. External users can browse events at `/events`
2. Click on an event to view details
3. Click "Register" to fill out registration form
4. Receive confirmation with registration code
5. Use registration code to search for registration status

## Setup Instructions

1. Run the migrations:
   ```bash
   php artisan migrate
   ```

2. Run the event seeder to create sample events:
   ```bash
   php artisan db:seed --class=EventSeeder
   ```

3. Ensure the storage link is created for image uploads:
   ```bash
   php artisan storage:link
   ```

## Hotel Slugs

Each hotel needs a unique slug for public event URLs. The seeder will automatically generate slugs based on hotel names. You can manually update hotel slugs in the database if needed.

## Security Features

- Event access is restricted by hotel (users can only see events from their hotel)
- Public events are validated to ensure they belong to the correct hotel
- Registration codes are unique and secure
- External users can only cancel their own registrations
- Admin users can manage all registrations for their hotel's events

## Future Enhancements

- Email notifications for registrations
- Payment integration for paid events
- Event categories and tags
- Recurring events
- Waitlist functionality
- Event reminders
- Integration with calendar systems

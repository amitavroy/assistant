# Assistant

A Laravel-based personal assistant application built with React and Inertia.js. This application helps you manage tasks and process newsletters from your Gmail account using AI-powered summarization and learning path generation.

## What is this Application?

Assistant is a productivity application that combines task management with intelligent newsletter processing. It allows you to:

- **Manage Tasks**: Create, organize, and track tasks with comments and reminders
- **Process Newsletters**: Automatically fetch newsletters from Gmail, generate AI-powered summaries, and create structured learning paths from technical content

The application uses Laravel 12 on the backend with React 19 on the frontend, connected via Inertia.js v2 for a seamless single-page application experience.

## Table of Contents

- [Installation](#installation)
- [Getting Started](#getting-started)
- [Features](#features)
  - [Newsletter Processing](#newsletter-processing)
    - [Fetch Newsletter from Gmail](#fetch-newsletter-from-gmail)
    - [Generate Summary](#generate-summary)
    - [Generate Learning Path](#generate-learning-path)
  - [Task Management](#task-management)
    - [Task Creation](#task-creation)
    - [Comments in Tasks](#comments-in-tasks)
    - [Reminders](#reminders)
- [Configuration](#configuration)
- [Development](#development)

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm
- A database (MySQL, PostgreSQL, or SQLite)
- Gmail account with IMAP access enabled (requires creating an [App password](https://support.google.com/accounts/answer/185833?hl=en))
- AI Provider API key (OpenAI, Anthropic, Gemini, or others supported by Neuron AI)

### Step 1: Clone the Repository

```bash
git clone https://github.com/amitavroy/assistant
cd assistant
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 3: Environment Configuration

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

### Step 4: Configure Environment Variables

Edit the `.env` file and configure the following:

#### Database Configuration

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=assistant
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### IMAP Configuration (for Gmail)

```env
IMAP_HOST=imap.gmail.com
IMAP_PORT=993
IMAP_USERNAME=your_email@gmail.com
IMAP_PASSWORD=your_app_password
IMAP_ENCRYPTION=ssl
```

**Note**: For Gmail, you'll need to generate an [App Password](https://support.google.com/accounts/answer/185833?hl=en) instead of using your regular password.

#### AI Provider Configuration

Choose one of the supported AI providers and configure it:

**OpenAI:**
```env
NEURON_AI_PROVIDER=openai
OPENAI_KEY=your_openai_api_key
OPENAI_MODEL=gpt-4
```

**Anthropic:**
```env
NEURON_AI_PROVIDER=anthropic
ANTHROPIC_KEY=your_anthropic_api_key
ANTHROPIC_MODEL=claude-3-7-sonnet-latest
```

**Other providers** (Ollama, Mistral, DeepSeek, HuggingFace) are also supported. See `config/neuron.php` for configuration options.

#### Queue Configuration

For processing newsletters in the background, configure your queue driver:

```env
QUEUE_CONNECTION=database
```

Or use Redis:

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Step 5: Run Migrations

```bash
php artisan migrate
```

### Step 6: Build Frontend Assets

```bash
npm run build
```

Or for development:

```bash
npm run dev
```

## Getting Started

### Start the Development Server

Run the development command which starts the Laravel server, queue worker, logs, and Vite dev server:

```bash
composer run dev
```

This will start:
- Laravel development server (usually at `http://localhost:8000`)
- Queue worker for background jobs
- Laravel Pail for log viewing
- Vite dev server for frontend assets

### Access the Application

1. Open your browser and navigate to `http://localhost:8000`
2. Register a new account or log in if you already have one
3. Start creating tasks or fetching newsletters!

### Running Queue Workers

For production or when testing newsletter processing, make sure to run the queue worker:

```bash
php artisan queue:work
```

Or use the included development command which runs it automatically.

## Features

### Newsletter Processing

The newsletter processing feature transforms your email newsletters into actionable learning resources using AI.

#### Fetch Newsletter from Gmail

Automatically fetch newsletters from your Gmail account using IMAP.

**How it works:**

1. The application connects to your Gmail account via IMAP
2. It retrieves emails from your inbox (typically newsletters)
3. Each email is stored as a newsletter entry in the database
4. The email content is preserved for further processing

**How to fetch newsletters:**

1. Navigate to the Newsletters page
2. Click "Fetch Mail" or use the fetch button
3. The application will queue a background job to fetch emails
4. Once complete, newsletters will appear in your newsletters list

**Manual fetch via command:**

You can also trigger email fetching via the command line:

```bash
php artisan app:email-digest
```

**Scheduled fetching:**

Set up a cron job or scheduler to automatically fetch newsletters:

```bash
# Add to your crontab
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Then schedule the command in `routes/console.php` or `app/Console/Kernel.php`.

#### Generate Summary

Automatically generate AI-powered summaries of newsletter content, filtering out promotional material and focusing on substantive, educational content.

**Features:**

- **Intelligent Filtering**: Removes subscription offers, promotions, testimonials, and marketing content
- **Content Extraction**: Identifies and extracts core educational or technical content
- **Structured Summary**: Generates summaries with three key sections:
  1. **Key Points**: Main takeaways from the newsletter
  2. **Summary**: A comprehensive overview of the content
  3. **Actionable Insights**: Practical steps or recommendations

**How summaries are generated:**

Summaries are automatically generated when newsletters are fetched. The application uses an AI agent (`EmailSummariserAgent`) that:

1. Analyzes the email content
2. Filters out promotional and non-essential content
3. Extracts the core educational material
4. Structures the information into the three-part format

Summaries are stored with each newsletter and can be viewed on the newsletter detail page. You can also download summaries as PDF files for offline reading and sharing.

#### Generate Learning Path

Transform newsletter content into structured learning paths, perfect for technical newsletters and educational content.

**Features:**

- **Logical Sequencing**: Analyzes content to determine natural learning dependencies
- **Granular Decomposition**: Breaks high-level concepts into micro-topics (10-15 minute deep dives)
- **Architectural Context**: Explains components and their relationships within larger systems
- **Knowledge Validation**: Includes active-learning exercises to reinforce understanding

**How learning paths are generated:**

1. Navigate to a newsletter that has been summarized
2. Click "Generate Learning Path"
3. The application queues a background job using the `NewsletterLearningPathAgent`
4. The AI agent analyzes the newsletter content and creates a structured curriculum
5. The learning path is stored and can be viewed on the newsletter detail page

**Learning Path Structure:**

The generated learning path follows a "First Principles" approach:

- **Problem Understanding**: Explains the "Why" before the "How"
- **Progressive Complexity**: Starts with foundational concepts and builds up
- **Practical Application**: Includes exercises and validation steps
- **System Context**: Shows how concepts fit into larger architectures

This feature is particularly useful for technical newsletters, system design content, and educational materials that benefit from structured learning approaches. Learning paths can be downloaded as PDF files for offline study and reference.

### Task Management

The application provides a comprehensive task management system to help you stay organized and productive.

#### Task Creation

Create tasks with the following information:

- **Description**: A detailed description of what needs to be done
- **Due Date**: Optional due date to track deadlines
- **Completion Status**: Mark tasks as completed or pending

Tasks are automatically associated with your user account and can be filtered to show completed or pending tasks.

**How to create a task:**

1. Navigate to the Tasks page from the sidebar
2. Click "Create Task" or use the create button
3. Fill in the task description and optionally set a due date
4. Save the task

#### Comments in Tasks

Add contextual comments to your tasks to track progress, add notes, or maintain a conversation thread about the task.

**Features:**

- Multiple comments per task
- Comments are stored as an array, allowing for rich conversation history
- Each comment is timestamped automatically
- Comments help you maintain context and track task-related discussions

**How to add a comment:**

1. Open a task from the tasks list
2. Scroll to the comments section
3. Enter your comment and submit
4. Your comment will be added to the task's comment history

#### Reminders

Set reminders for your tasks to ensure you never miss important deadlines or follow-ups.

**Features:**

- Set a `next_reminder` datetime for any task
- Update reminders as needed
- Reminders help you prioritize and schedule your work

**How to set a reminder:**

1. Open a task
2. Navigate to the reminder section
3. Set your desired reminder date and time
4. Save the reminder

The reminder system allows you to schedule follow-ups and ensure important tasks don't get forgotten.

## Configuration

### IMAP Settings

Configure IMAP settings in `config/imap.php` or via environment variables. The default configuration supports Gmail, but can be adapted for other email providers.

### AI Provider Settings

Configure your preferred AI provider in `config/neuron.php`. The application supports multiple providers:

- OpenAI
- Anthropic (Claude)
- Google Gemini
- Ollama (local)
- Mistral
- DeepSeek
- HuggingFace

Switch providers by changing the `NEURON_AI_PROVIDER` environment variable.

### Queue Configuration

Newsletter processing runs as background jobs. Configure your queue driver in `.env`:

- `database`: Uses database tables (default, no additional setup)
- `redis`: Requires Redis server
- `sqs`: Amazon SQS
- `sync`: Synchronous (for testing only)

## Development

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter TaskTest

# Run with coverage
php artisan test --coverage
```

### Code Formatting

```bash
# Format PHP code
vendor/bin/pint

# Format JavaScript/TypeScript code
npm run format
```

### Code Linting

```bash
# Lint PHP code
vendor/bin/pint --test

# Lint JavaScript/TypeScript code
npm run lint
```

### Type Checking

```bash
# Check TypeScript types
npm run types
```

### Database

```bash
# Create a new migration
php artisan make:migration create_example_table

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Create a seeder
php artisan make:seeder ExampleSeeder

# Run seeders
php artisan db:seed
```

## Technology Stack

- **Backend**: Laravel 12
- **Frontend**: React 19 with TypeScript
- **Framework**: Inertia.js v2
- **Authentication**: Laravel Fortify
- **AI Framework**: Neuron AI
- **Email**: IMAP Engine Laravel
- **Styling**: Tailwind CSS v4
- **Testing**: Pest PHP v4
- **Code Quality**: Laravel Pint, ESLint, Prettier

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

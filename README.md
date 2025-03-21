# Upwork RSS Feed Generator for Make.com

This PHP script is designed to work as part of a **Make.com (formerly Integromat)** automation scenario. It takes raw HTML content from an Upwork job search page, parses the data, and generates an RSS feed. If the feed is created for the first time, it also sends the feed URL to the user's email.

## 🔧 Features

- Designed specifically for **Make.com HTTP module**
- Parses Upwork job listings (title, description, publish date)
- Generates a valid RSS feed (XML)
- Sends RSS feed URL via email on first-time creation
- Fully dynamic paths & domain support
- No external dependencies (pure PHP)

## 🤖 How It Works in Make.com

1. **HTTP Module #1**  
   Fetch HTML from an Upwork job search URL

2. **HTTP Module #2**  
   Send a POST request to this PHP script with:
   - `output` (the raw HTML from the first HTTP request)
   - `feed_name` (used to create a `.xml` file)
   - `email_address` (to email the feed URL)

## 📥 POST Parameters

| Parameter       | Required | Description                                 |
|----------------|----------|---------------------------------------------|
| `output`        | ✅       | The full HTML of the Upwork job listing page |
| `feed_name`     | ✅       | The name of the RSS feed file (without extension) |
| `email_address` | ✅       | The user's email address to send the feed link |

## 📂 Installation

1. Upload the script to your server.
2. Make sure the `/feed/` directory exists and is writable.
3. Use the URL to this script in Make.com's HTTP request module.

## 📧 Email Configuration

- Uses PHP's native `mail()` function
- No third-party mailer libraries needed
- Make sure `mail()` is enabled and configured on your server

## 💬 Example Response

- `Check your email for the RSS feed URL` (if feed was created)
- `RSS feed updated successfully.` (if feed already existed)

---

## ❤️ Built for Automators

This script is ideal for freelancers, agencies, and developers who want to stay up-to-date with new jobs on Upwork — directly in their RSS reader, automatically via Make.com.

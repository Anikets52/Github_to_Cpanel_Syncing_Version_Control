# GitHub to cPanel Syncing – Version Control

This repository explains **multiple ways to deploy and sync code from a GitHub repository to a cPanel-hosted server** whenever new code is pushed or updated.  
It is useful for developers who host PHP or web projects on shared hosting using **cPanel** and want a reliable deployment workflow.

---

## 📌 Overview

cPanel provides built-in Git Version Control, but there are **different deployment strategies** depending on your needs—manual, semi-automatic, or fully automated.

This guide covers **three proven methods** to sync code from GitHub to cPanel.

---

## 🚀 Deployment Methods

### 1️⃣ Manual Deployment via cPanel Git Version Control

**Best for:** Beginners or low-frequency deployments.

**How it works:**
- Use cPanel’s built-in **Git Version Control**
- Pull the latest changes manually

**Steps:**
1. Login to **cPanel**
2. Open **Git™ Version Control**
3. Select your repository
4. Click **Deploy HEAD Commit**

**Pros:**
- Simple and safe
- No server-side scripting required

**Cons:**
- Manual process  
- Not suitable for frequent updates

---

### 2️⃣ Automatic Deployment Using Custom GitHub Webhook (`deploy.php`)

**Best for:** Automated deployments on every GitHub push.

**How it works:**
- GitHub sends a webhook request on every push
- A custom `deploy.php` script on the server:
  - Validates the webhook secret
  - Pulls the latest code
  - Optionally sends email notifications

**Key Features:**
- Secure webhook secret validation
- Branch-specific deployment
- SSH-based Git pull
- Email alerts on success/failure

**Pros:**
- Fully automatic
- Real-time deployment
- Ideal for active development

**Cons:**
- Requires SSH access
- Needs careful security configuration

---

### 3️⃣ Automated Deployment Using cPanel Cron Jobs

**Best for:** Environments where webhooks are restricted or unavailable.

**How it works:**
- A cron job runs at a fixed interval
- The job checks for new commits and pulls updates

**Example Use Case:**
- Pull latest code every 5 or 10 minutes

**Pros:**
- Easy to configure
- No external webhook dependency

**Cons:**
- Not instant (time-based)
- Pulls even when no changes exist

---

## 🧩 When to Use Which Method?

| Scenario | Recommended Method |
|--------|-------------------|
| Small / static site | Manual Deployment |
| Active development | Webhook (`deploy.php`) |
| No webhook access | Cron Job |

---

## 🔐 Security Notes

- Always protect webhook endpoints with a **secret**
- Restrict file permissions for deployment scripts
- Never expose SSH credentials in public repositories
- Use branch-based deployment (e.g., `main` / `master` only)

---


## 📖 References

- cPanel Git™ Version Control
- GitHub Webhooks
- Linux Cron Jobs
- Automatic deploy script by oxguy3: https://gist.github.com/oxguy3/70ea582d951d4b0f78edec282a2bebf9

---

## ✅ Conclusion

This repository demonstrates **flexible and production-ready ways** to sync GitHub repositories with cPanel hosting—ranging from manual control to fully automated CI-like deployments.

Choose the method that best fits your workflow and hosting limitations.


